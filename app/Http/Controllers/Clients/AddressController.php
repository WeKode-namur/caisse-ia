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
     * Affiche le formulaire de création d'adresse pour un client
     */
    public function createForCustomer(Customer $customer)
    {
        return view('panel.clients.addresses.create', compact('customer'));
    }

    /**
     * Affiche le formulaire de création d'adresse pour une entreprise
     */
    public function createForCompany(Company $company)
    {
        return view('panel.clients.addresses.create', compact('company'));
    }

    /**
     * Enregistre une nouvelle adresse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'type' => ['required', Rule::in(array_keys(CustomerAddress::ADDRESS_TYPES))],
            'street' => ['required', 'string', 'max:255'],
            'number' => ['required', 'string', 'max:10'],
            'city' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:10'],
            'country' => ['required', 'string', 'max:100'],
            'is_primary' => ['boolean'],
        ]);

        // Vérification qu'exactement un client OU une entreprise est spécifié
        if (empty($validated['customer_id']) && empty($validated['company_id'])) {
            return back()->withErrors(['customer_id' => 'Vous devez spécifier un client ou une entreprise.']);
        }

        if (!empty($validated['customer_id']) && !empty($validated['company_id'])) {
            return back()->withErrors(['customer_id' => 'Vous ne pouvez pas spécifier à la fois un client et une entreprise.']);
        }

        $address = CustomerAddress::create($validated);

        // Si c'est l'adresse principale, on retire le statut principal des autres
        if ($address->is_primary) {
            $address->setAsPrimary();
        }

        $redirectRoute = $validated['customer_id'] 
            ? route('clients.customers.show', $validated['customer_id'])
            : route('clients.companies.show', $validated['company_id']);

        return redirect($redirectRoute)
            ->with('success', 'Adresse ajoutée avec succès.');
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(CustomerAddress $address)
    {
        $address->load(['customer', 'company']);
        return view('panel.clients.addresses.edit', compact('address'));
    }

    /**
     * Met à jour une adresse
     */
    public function update(Request $request, CustomerAddress $address)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(array_keys(CustomerAddress::ADDRESS_TYPES))],
            'street' => ['required', 'string', 'max:255'],
            'number' => ['required', 'string', 'max:10'],
            'city' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:10'],
            'country' => ['required', 'string', 'max:100'],
            'is_primary' => ['boolean'],
        ]);

        $address->update($validated);

        // Si c'est l'adresse principale, on retire le statut principal des autres
        if ($address->is_primary) {
            $address->setAsPrimary();
        }

        $redirectRoute = $address->customer_id 
            ? route('clients.customers.show', $address->customer_id)
            : route('clients.companies.show', $address->company_id);

        return redirect($redirectRoute)
            ->with('success', 'Adresse mise à jour avec succès.');
    }

    /**
     * Supprime une adresse
     */
    public function destroy(CustomerAddress $address)
    {
        $customerId = $address->customer_id;
        $companyId = $address->company_id;

        $address->delete();

        $redirectRoute = $customerId 
            ? route('clients.customers.show', $customerId)
            : route('clients.companies.show', $companyId);

        return redirect($redirectRoute)
            ->with('success', 'Adresse supprimée avec succès.');
    }

    /**
     * Définit une adresse comme principale
     */
    public function setPrimary(CustomerAddress $address)
    {
        $address->setAsPrimary();

        $redirectRoute = $address->customer_id 
            ? route('clients.customers.show', $address->customer_id)
            : route('clients.companies.show', $address->company_id);

        return redirect($redirectRoute)
            ->with('success', 'Adresse définie comme principale.');
    }
} 