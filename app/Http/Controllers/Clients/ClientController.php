<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ClientController extends Controller
{
    /**
     * Affiche la page principale des clients
     */
    public function index(Request $request)
    {
        // Si c'est une requête AJAX, retourner le tableau
        if ($request->ajax()) {
            return $this->getTableData($request);
        }

        // Sinon, afficher la page complète
        return view('panel.clients.index');
    }

    /**
     * Retourne les données du tableau en AJAX
     */
    public function getTableData(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'active'); // Par défaut 'active'
        $type = $request->get('type', 'all');
        $sort = $request->get('sort', 'name');
        $page = $request->get('page', 1);

        // Récupération des clients particuliers
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
            ->get()
            ->map(function ($customer) {
                $customer->type = 'customer';
                $customer->display_name = $customer->full_name;
                $customer->number = $customer->customer_number;
                $customer->email = $customer->email;
                $customer->phone = $customer->phone;
                return $customer;
            });

        // Récupération des entreprises
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
            ->get()
            ->map(function ($company) {
                $company->type = 'company';
                $company->display_name = $company->name;
                $company->number = $company->company_number;
                $company->email = $company->email;
                $company->phone = $company->phone;
                return $company;
            });

        // Fusion des deux collections
        $allClients = $customers->concat($companies);

        // Filtre par type si besoin
        if ($type !== 'all') {
            $allClients = $allClients->filter(function ($client) use ($type) {
                return $client->type === $type;
            });
        }

        // Tri global sur la collection fusionnée
        $allClients = $allClients->sortBy(function ($client) use ($sort) {
            if ($sort === 'name') {
                return mb_strtolower($client->display_name);
            } elseif ($sort === 'created_at') {
                return -strtotime($client->created_at ?? '');
            } elseif ($sort === 'loyalty_points') {
                return -($client->loyalty_points ?? 0);
            }
            return mb_strtolower($client->display_name);
        })->values();

        // Pagination manuelle
        $perPage = 15;
        $offset = ($page - 1) * $perPage;
        $paginatedClients = $allClients->slice($offset, $perPage);
        $total = $allClients->count();
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedClients,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('panel.clients.partials.table', compact('paginator', 'search', 'status', 'type', 'sort'));
    }

    /**
     * Retourne les statistiques en AJAX
     */
    public function getStats(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'all');
        $type = $request->get('type', 'all');

        // Base queries
        $customerQuery = Customer::query();
        $companyQuery = Company::query();

        // Appliquer les filtres
        if ($search) {
            $customerQuery->search($search);
            $companyQuery->search($search);
        }

        if ($status !== 'all') {
            if ($status === 'active') {
                $customerQuery->active();
                $companyQuery->active();
            } elseif ($status === 'inactive') {
                $customerQuery->where('is_active', false);
                $companyQuery->where('is_active', false);
            }
        }

        // Calculer les statistiques
        $stats = [
            'total_customers' => $customerQuery->count(),
            'total_companies' => $companyQuery->count(),
            'new_this_week' => Customer::where('created_at', '>=', Carbon::now()->startOfWeek())->count() +
                              Company::where('created_at', '>=', Carbon::now()->startOfWeek())->count(),
            'total_loyalty_points' => Customer::sum('loyalty_points') + Company::sum('loyalty_points'),
        ];

        return response()->json($stats);
    }

    /**
     * Affiche le formulaire de création unifié
     */
    public function create()
    {
        return view('panel.clients.create');
    }

    /**
     * Enregistre un nouveau client (particulier ou entreprise)
     */
    public function store(Request $request)
    {
        $clientType = $request->input('client_type');

        if ($clientType === 'customer') {
            return $this->storeCustomer($request);
        } elseif ($clientType === 'company') {
            return $this->storeCompany($request);
        }

        return back()->withErrors(['client_type' => 'Type de client invalide']);
    }

    /**
     * Enregistre un client particulier
     */
    private function storeCustomer(Request $request)
    {
        $request->merge([
            'marketing_consent' => $request->has('marketing_consent'),
            'is_active' => $request->has('is_active'),
        ]);
        $messages = [
            'gender.in' => 'Le genre sélectionné est invalide.',
            'first_name.required' => 'Le prénom est obligatoire.',
            'first_name.max' => 'Le prénom ne peut pas dépasser 100 caractères.',
            'last_name.required' => 'Le nom est obligatoire.',
            'last_name.max' => 'Le nom ne peut pas dépasser 100 caractères.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.max' => 'L\'adresse email ne peut pas dépasser 320 caractères.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'phone.max' => 'Le téléphone ne peut pas dépasser 20 caractères.',
            'birth_date.date' => 'La date de naissance doit être une date valide.',
            'marketing_consent.boolean' => 'Le consentement marketing doit être vrai ou faux.',
            'notes.string' => 'Les notes doivent être une chaîne de caractères.',
            'is_active.boolean' => 'Le statut actif doit être vrai ou faux.',
        ];
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
        ], $messages);
        $customer = Customer::create($validated);
        return redirect()
            ->route('clients.customers.show', $customer)
            ->with('success', 'Client créé avec succès.');
    }

    /**
     * Enregistre une entreprise
     */
    private function storeCompany(Request $request)
    {
        $request->merge([
            'is_active' => $request->has('is_active'),
        ]);
        $messages = [
            'name.required' => 'Le nom de l\'entreprise est obligatoire.',
            'name.max' => 'Le nom de l\'entreprise ne peut pas dépasser 255 caractères.',
            'legal_name.max' => 'Le nom légal ne peut pas dépasser 255 caractères.',
            'company_number_be.max' => 'Le numéro d\'entreprise BE ne peut pas dépasser 15 caractères.',
            'company_number_be.unique' => 'Ce numéro d\'entreprise BE est déjà utilisé.',
            'vat_number.max' => 'Le numéro de TVA ne peut pas dépasser 15 caractères.',
            'vat_number.unique' => 'Ce numéro de TVA est déjà utilisé.',
            'company_type.in' => 'Le type d\'entreprise sélectionné est invalide.',
            'legal_representative.max' => 'Le représentant légal ne peut pas dépasser 255 caractères.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.max' => 'L\'adresse email ne peut pas dépasser 320 caractères.',
            'phone.max' => 'Le téléphone ne peut pas dépasser 20 caractères.',
            'payment_terms.integer' => 'Les conditions de paiement doivent être un nombre entier.',
            'payment_terms.min' => 'Les conditions de paiement doivent être au moins 0.',
            'payment_terms.max' => 'Les conditions de paiement ne peuvent pas dépasser 365.',
            'credit_limit.numeric' => 'La limite de crédit doit être un nombre.',
            'credit_limit.min' => 'La limite de crédit doit être au moins 0.',
            'notes.string' => 'Les notes doivent être une chaîne de caractères.',
            'is_active.boolean' => 'Le statut actif doit être vrai ou faux.',
        ];
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
        ], $messages);
        $company = Company::create($validated);
        return redirect()
            ->route('clients.companies.show', $company)
            ->with('success', 'Entreprise créée avec succès.');
    }

    /**
     * Recherche AJAX de clients (particuliers et entreprises)
     */
    public function search(Request $request)
    {
        $q = $request->get('q');
        if (!$q || strlen($q) < 2) {
            return response()->json([]);
        }
        $customers = collect(Customer::query()
            ->where(function ($query) use ($q) {
                $query->where('first_name', 'like', "%$q%")
                    ->orWhere('last_name', 'like', "%$q%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%$q%"])
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('phone', 'like', "%$q%");
            })
            ->limit(10)
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'type' => 'customer',
                    'name' => $c->first_name . ' ' . $c->last_name,
                    'display_name' => $c->first_name . ' ' . $c->last_name,
                    'number' => $c->customer_number,
                    'email' => $c->email,
                    'phone' => $c->phone,
                    'loyalty_points' => $c->loyalty_points ?? 0,
                    'is_active' => $c->is_active,
                    'first_name' => $c->first_name,
                    'last_name' => $c->last_name,
                ];
            }));

        $companies = collect(Company::query()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%$q%")
                    ->orWhere('legal_name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('phone', 'like', "%$q%")
                    ->orWhere('company_number_be', 'like', "%$q%")
                    ->orWhere('vat_number', 'like', "%$q%");
            })
            ->limit(10)
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'type' => 'company',
                    'name' => $c->name,
                    'display_name' => $c->name,
                    'number' => $c->company_number_be,
                    'email' => $c->email,
                    'phone' => $c->phone,
                    'loyalty_points' => $c->loyalty_points ?? 0,
                    'is_active' => $c->is_active,
                    'company_number_be' => $c->company_number_be,
                ];
            }));

        $results = $customers->merge($companies)->values();
        return response()->json($results);
    }
}
