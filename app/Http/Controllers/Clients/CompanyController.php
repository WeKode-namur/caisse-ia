<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    /**
     * Affiche la liste des entreprises
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'all');

        $companies = Company::query()
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

        return view('panel.clients.companies.index', compact('companies', 'search', 'status'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        return view('panel.clients.companies.create');
    }

    /**
     * Enregistre une nouvelle entreprise
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'company_number_be' => ['nullable', 'string', 'max:15', 'unique:companies'],
            'vat_number' => ['nullable', 'string', 'max:15', 'unique:companies'],
            'company_type' => ['nullable', Rule::in(array_keys(Company::COMPANY_TYPES))],
            'legal_representative' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:320'],
            'phone' => ['nullable', 'string', 'max:20'],
            'payment_terms' => ['integer', 'min:0', 'max:365'],
            'credit_limit' => ['numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $company = Company::create($validated);

        return redirect()
            ->route('clients.companies.show', $company)
            ->with('success', 'Entreprise créée avec succès.');
    }

    /**
     * Affiche les détails d'une entreprise
     */
    public function show(Company $company)
    {
        $company->load([
            'addresses',
            'loyaltyPoints' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            },
            'transactions' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            }
        ]);

        return view('panel.clients.companies.show', compact('company'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Company $company)
    {
        $company->load('addresses');
        return view('panel.clients.companies.edit', compact('company'));
    }

    /**
     * Met à jour une entreprise
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'company_number_be' => ['nullable', 'string', 'max:15', Rule::unique('companies')->ignore($company)],
            'vat_number' => ['nullable', 'string', 'max:15', Rule::unique('companies')->ignore($company)],
            'company_type' => ['nullable', Rule::in(array_keys(Company::COMPANY_TYPES))],
            'legal_representative' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:320'],
            'phone' => ['nullable', 'string', 'max:20'],
            'payment_terms' => ['integer', 'min:0', 'max:365'],
            'credit_limit' => ['numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $company->update($validated);

        return redirect()
            ->route('clients.companies.show', $company)
            ->with('success', 'Entreprise mise à jour avec succès.');
    }

    /**
     * Supprime une entreprise
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()
            ->route('clients.companies.index')
            ->with('success', 'Entreprise supprimée avec succès.');
    }

    /**
     * Recherche d'entreprises pour l'API
     */
    public function search(Request $request)
    {
        $search = $request->get('q');
        
        if (empty($search)) {
            return response()->json([]);
        }

        $companies = Company::active()
            ->search($search)
            ->select('id', 'company_number', 'name', 'legal_name', 'email', 'phone')
            ->limit(10)
            ->get()
            ->map(function ($company) {
                return [
                    'id' => $company->id,
                    'text' => $company->name . ' (' . $company->company_number . ')',
                    'company_number' => $company->company_number,
                    'name' => $company->name,
                    'legal_name' => $company->legal_name,
                    'email' => $company->email,
                    'phone' => $company->phone,
                ];
            });

        return response()->json($companies);
    }
} 