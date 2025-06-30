<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Company;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AddressController extends Controller
{
    /**
     * Charge les adresses d'un client en AJAX
     */
    public function getCustomerAddresses(Customer $customer)
    {
        $addresses = CustomerAddress::where('customer_id', $customer->id)
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
        
        return response()->json([
            'addresses' => $addresses,
            'address_types' => CustomerAddress::ADDRESS_TYPES
        ]);
    }

    /**
     * Charge les adresses d'une entreprise en AJAX
     */
    public function getCompanyAddresses(Company $company)
    {
        $addresses = CustomerAddress::where('company_id', $company->id)
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
        
        return response()->json([
            'addresses' => $addresses,
            'address_types' => CustomerAddress::ADDRESS_TYPES
        ]);
    }

    /**
     * Affiche le formulaire de création d'adresse pour un client
     */
    public function createForCustomer(Customer $customer)
    {
        return view('panel.clients.addresses.create', [
            'client' => $customer,
            'clientType' => 'customer'
        ]);
    }

    /**
     * Affiche le formulaire de création d'adresse pour une entreprise
     */
    public function createForCompany(Company $company)
    {
        return view('panel.clients.addresses.create', [
            'client' => $company,
            'clientType' => 'company'
        ]);
    }

    /**
     * Enregistre une nouvelle adresse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_type' => ['required', 'in:customer,company'],
            'client_id' => ['required', 'integer'],
            'type' => ['required', Rule::in(array_keys(CustomerAddress::ADDRESS_TYPES))],
            'street' => ['required', 'string', 'max:255'],
            'number' => ['required', 'string', 'max:20'],
            'postal_code' => ['required', 'string', 'max:10'],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'is_primary' => ['boolean'],
        ]);

        // Préparer les données pour l'insertion
        $addressData = [
            'type' => $validated['type'],
            'street' => $validated['street'],
            'number' => $validated['number'],
            'postal_code' => $validated['postal_code'],
            'city' => $validated['city'],
            'country' => $validated['country'],
            'is_primary' => $validated['is_primary'] ?? false,
        ];

        // Ajouter l'ID du client ou de l'entreprise
        if ($validated['client_type'] === 'customer') {
            $addressData['customer_id'] = $validated['client_id'];
            $addressData['company_id'] = null;
        } else {
            $addressData['company_id'] = $validated['client_id'];
            $addressData['customer_id'] = null;
        }

        // Si c'est une adresse principale, désactiver les autres adresses principales du même type
        if ($addressData['is_primary']) {
            $query = CustomerAddress::where('type', $validated['type']);
            
            if ($validated['client_type'] === 'customer') {
                $query->where('customer_id', $validated['client_id']);
            } else {
                $query->where('company_id', $validated['client_id']);
            }
            
            $query->update(['is_primary' => false]);
        }

        $address = CustomerAddress::create($addressData);

        return response()->json([
            'success' => true,
            'message' => 'Adresse ajoutée avec succès',
            'address' => $address
        ]);
    }

    /**
     * Afficher le formulaire d'édition d'une adresse
     */
    public function edit($id)
    {
        $address = CustomerAddress::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'address' => $address
        ]);
    }

    /**
     * Mettre à jour une adresse
     */
    public function update(Request $request, $id)
    {
        $address = CustomerAddress::findOrFail($id);
        
        $request->validate([
            'type' => 'required|string|max:50',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'postal_code' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'is_primary' => 'boolean'
        ]);
        
        // Si cette adresse devient principale, retirer le statut principal des autres adresses
        if ($request->has('is_primary') && $request->is_primary) {
            if ($address->customer_id) {
                CustomerAddress::where('customer_id', $address->customer_id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_primary' => false]);
            } elseif ($address->company_id) {
                CustomerAddress::where('company_id', $address->company_id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_primary' => false]);
            }
        }
        
        $address->update([
            'type' => $request->type,
            'street' => $request->street,
            'number' => $request->number,
            'postal_code' => $request->postal_code,
            'city' => $request->city,
            'country' => $request->country,
            'is_primary' => $request->has('is_primary') ? $request->is_primary : false,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Adresse mise à jour avec succès'
        ]);
    }

    /**
     * Définit une adresse comme principale
     */
    public function setPrimary($id)
    {
        $address = CustomerAddress::findOrFail($id);
        
        // Désactiver toutes les autres adresses principales du même type
        $query = CustomerAddress::where('type', $address->type);
        
        if ($address->customer_id) {
            $query->where('customer_id', $address->customer_id);
        } else {
            $query->where('company_id', $address->company_id);
        }
        
        $query->where('id', '!=', $address->id)
              ->update(['is_primary' => false]);

        // Activer cette adresse comme principale
        $address->update(['is_primary' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Adresse définie comme principale'
        ]);
    }

    /**
     * Supprime une adresse
     */
    public function destroy($id)
    {
        $address = CustomerAddress::findOrFail($id);
        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Adresse supprimée avec succès'
        ]);
    }

    /**
     * Charge les adresses d'un client ou d'une entreprise en AJAX
     */
    public function index($type, $id)
    {
        // Corriger les noms de colonnes
        $columnName = $type === 'customers' ? 'customer_id' : 'company_id';
        
        $addresses = CustomerAddress::where($columnName, $id)
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
        
        return response()->json([
            'addresses' => $addresses,
            'address_types' => CustomerAddress::ADDRESS_TYPES
        ]);
    }
} 