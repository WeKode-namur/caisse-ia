<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ClientController extends Controller
{
    /**
     * Affiche la liste unifiée des clients et entreprises
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'all');
        $type = $request->get('type', 'all');
        $sort = $request->get('sort', 'name');

        // Statistiques
        $stats = [
            'total_customers' => Customer::active()->count(),
            'total_companies' => Company::active()->count(),
            'new_this_week' => Customer::where('created_at', '>=', Carbon::now()->startOfWeek())->count() +
                              Company::where('created_at', '>=', Carbon::now()->startOfWeek())->count(),
            'total_loyalty_points' => Customer::sum('loyalty_points') + Company::sum('loyalty_points'),
        ];

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
            ->when($sort === 'name', function ($query) {
                $query->orderBy('first_name')->orderBy('last_name');
            })
            ->when($sort === 'created_at', function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->when($sort === 'loyalty_points', function ($query) {
                $query->orderBy('loyalty_points', 'desc');
            })
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
            ->when($sort === 'name', function ($query) {
                $query->orderBy('name');
            })
            ->when($sort === 'created_at', function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->when($sort === 'loyalty_points', function ($query) {
                $query->orderBy('loyalty_points', 'desc');
            })
            ->get()
            ->map(function ($company) {
                $company->type = 'company';
                $company->display_name = $company->name;
                $company->number = $company->company_number;
                return $company;
            });

        // Fusion et tri des collections
        $allClients = $customers->concat($companies);
        
        if ($type !== 'all') {
            $allClients = $allClients->filter(function ($client) use ($type) {
                return $client->type === $type;
            });
        }

        // Pagination manuelle
        $perPage = 25;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $paginatedClients = $allClients->slice($offset, $perPage);
        $total = $allClients->count();
        
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedClients,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('panel.clients.index', compact('paginator', 'stats', 'search', 'status', 'type', 'sort'));
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
     * Enregistre une entreprise
     */
    private function storeCompany(Request $request)
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
} 