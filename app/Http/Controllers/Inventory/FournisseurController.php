<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class FournisseurController extends Controller
{
    private function checkEnabled()
    {
        if (!Config::get('custom.suppliers_enabled')) {
            abort(404);
        }
    }

    public function index()
    {
        $this->checkEnabled();
        return Fournisseur::all();
    }

    public function search(Request $request)
    {
        $this->checkEnabled();
        $q = $request->input('q');
        return Fournisseur::where('name', 'like', "%$q%")
            ->orderBy('name')
            ->limit(10)
            ->get();
    }

    public function store(Request $request)
    {
        $this->checkEnabled();
        $data = $request->validate(Fournisseur::rules());
        $data['slug'] = Str::slug($data['name']);
        $fournisseur = Fournisseur::create($data);
        return response()->json($fournisseur, 201);
    }

    public function show($id)
    {
        $this->checkEnabled();
        return Fournisseur::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $this->checkEnabled();
        $fournisseur = Fournisseur::findOrFail($id);
        $data = $request->validate(Fournisseur::rules($id));
        $data['slug'] = Str::slug($data['name']);
        $fournisseur->update($data);
        return response()->json($fournisseur);
    }

    public function destroy($id)
    {
        $this->checkEnabled();
        $fournisseur = Fournisseur::findOrFail($id);
        $fournisseur->delete();
        return response()->json(['success' => true]);
    }
} 