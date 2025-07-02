<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Type;
use App\Models\Subtype;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleEditController extends Controller
{
    // Affiche les infos pour le modal d'édition (AJAX)
    public function edit($id)
    {
        $article = Article::with(['category', 'type', 'subtype'])->findOrFail($id);
        $categories = Category::all();
        $types = $article->category ? Type::where('category_id', $article->category_id)->get() : collect();
        $subtypes = $article->type ? Subtype::where('type_id', $article->type_id)->get() : collect();
        return response()->json([
            'article' => $article,
            'categories' => $categories,
            'types' => $types,
            'subtypes' => $subtypes,
        ]);
    }

    // Sauvegarde les modifications
    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tva' => 'required|integer|min:0|max:100',
            'category_id' => 'required|exists:categories,id',
            'type_id' => 'nullable|exists:types,id',
            'subtype_id' => 'nullable|exists:subtypes,id',
            'description' => 'nullable|string|max:1000',
            'sell_price' => 'nullable|numeric|min:0',
        ]);
        DB::beginTransaction();
        try {
            $old = $article->only(['name','tva','category_id','type_id','subtype_id','description','sell_price']);
            $article->update($validated);
            DB::commit();
            return response()->json([
                'success' => true,
                'old' => $old,
                'new' => $article->only(['name','tva','category_id','type_id','subtype_id','description','sell_price'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
} 