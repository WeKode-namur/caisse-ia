<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\{Category, PaymentMethod, CashRegister};
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * Affiche l'interface de caisse
     */
    public function index()
    {
        // Récupérer les données nécessaires pour l'initialisation
        $categories = Category::orderBy('name')->get();
        $paymentMethods = PaymentMethod::active()->ordered()->get();
        $cashRegisters = CashRegister::active()->get();

        // Vérifier qu'un utilisateur est connecté et a les permissions
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Sélectionner une caisse par défaut si pas encore définie en session
        if (!session('current_cash_register_id')) {
            $defaultRegister = $cashRegisters->first();
            if ($defaultRegister) {
                session(['current_cash_register_id' => $defaultRegister->id]);
            }
        }

        // Vérifier si la gestion des clients est activée
        $customerManagementEnabled = config('app.register_customer_management', false);

        return view('panel.register.view', compact(
            'categories',
            'paymentMethods',
            'cashRegisters',
            'user',
            'customerManagementEnabled'
        ));
    }

    /**
     * Change la caisse active
     */
    public function switchCashRegister(Request $request)
    {
        $request->validate([
            'cash_register_id' => 'required|exists:cash_registers,id'
        ]);

        $cashRegister = CashRegister::findOrFail($request->cash_register_id);

        if (!$cashRegister->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Cette caisse n\'est pas active.'
            ], 422);
        }

        session(['current_cash_register_id' => $cashRegister->id]);

        return response()->json([
            'success' => true,
            'message' => "Caisse changée vers : {$cashRegister->name}",
            'cash_register' => $cashRegister
        ]);
    }

    /**
     * Obtient les informations de la session caisse courante
     */
    public function getSessionInfo()
    {
        $user = auth()->user();
        $cashRegisterId = session('current_cash_register_id');
        $cashRegister = $cashRegisterId ? CashRegister::find($cashRegisterId) : null;

        // Récupérer le panier de la session
        $cart = session('register_cart', []);
        $cartCount = collect($cart)->sum('quantity');

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'username' => $user->username
            ],
            'cash_register' => $cashRegister ? [
                'id' => $cashRegister->id,
                'name' => $cashRegister->name,
                'location' => $cashRegister->location
            ] : null,
            'cart' => [
                'items_count' => $cartCount,
                'total_items' => count($cart)
            ],
            'session_id' => session()->getId()
        ]);
    }
}
