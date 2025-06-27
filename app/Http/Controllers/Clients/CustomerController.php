<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Affiche la liste des clients
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'all');

        $customers = Customer::query()
            ->when($search, function ($query) use ($search) {
                $query->search($search);
            })
            ->when($status !== 'all', function ($query) use ($status) {
                if ($status === 'active') {
                    $query->active();
                } elseif ($status === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->with(['billingAddress', 'shippingAddress'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('panel.clients.customers.index', compact('customers', 'search', 'status'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        return view('panel.clients.customers.create');
    }

    /**
     * Enregistre un nouveau client
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'gender' => ['nullable', Rule::in(array_keys(Customer::GENDERS))],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:320', 'unique:customers'],
            'phone' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'marketing_consent' => ['boolean'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $customer = Customer::create($validated);

        return redirect()
            ->route('clients.customers.show', $customer)
            ->with('success', 'Client créé avec succès.');
    }

    /**
     * Affiche les détails d'un client
     */
    public function show(Customer $customer)
    {
        $customer->load([
            'addresses',
            'loyaltyPoints' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            },
            'transactions' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            }
        ]);

        return view('panel.clients.customers.show', compact('customer'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Customer $customer)
    {
        $customer->load('addresses');
        return view('panel.clients.customers.edit', compact('customer'));
    }

    /**
     * Met à jour un client
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'gender' => ['nullable', Rule::in(array_keys(Customer::GENDERS))],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:320', Rule::unique('customers')->ignore($customer)],
            'phone' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'marketing_consent' => ['boolean'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $customer->update($validated);

        return redirect()
            ->route('clients.customers.show', $customer)
            ->with('success', 'Client mis à jour avec succès.');
    }

    /**
     * Supprime un client
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('clients.customers.index')
            ->with('success', 'Client supprimé avec succès.');
    }

    /**
     * Recherche de clients pour l'API
     */
    public function search(Request $request)
    {
        $search = $request->get('q');
        
        if (empty($search)) {
            return response()->json([]);
        }

        $customers = Customer::active()
            ->search($search)
            ->select('id', 'customer_number', 'first_name', 'last_name', 'email', 'phone')
            ->limit(10)
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'text' => $customer->full_name . ' (' . $customer->customer_number . ')',
                    'customer_number' => $customer->customer_number,
                    'full_name' => $customer->full_name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                ];
            });

        return response()->json($customers);
    }
} 