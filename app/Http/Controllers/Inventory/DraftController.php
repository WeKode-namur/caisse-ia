<?php
namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class DraftController extends Controller
{
    /**
     * Page principale de création avec gestion des brouillons
     */
    public function index()
    {
        return view('panel.inventory.drafts');
    }

    /**
     * Liste des brouillons (AJAX)
     */
    public function getDrafts(Request $request)
    {
        $query = Article::where('status', Article::STATUS_DRAFT)
            ->orderBy('updated_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        $drafts = $query->paginate(1);

        return view('panel.inventory.partials.drafts-table', compact('drafts'))->render();
    }

    /**
     * Supprimer un brouillon
     */
    public function destroy($id)
    {
        $draft = Article::where('status', Article::STATUS_DRAFT)
            ->findOrFail($id);

        $draft->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Dupliquer un brouillon
     */
    public function duplicate($id)
    {
        $originalDraft = Article::where('status', Article::STATUS_DRAFT)
            ->with(['variants.stocks', 'variants.attributeValues'])
            ->findOrFail($id);

        $newDraft = $originalDraft->replicate();
        $newDraft->name = $originalDraft->name . ' (Copie)';
        $newDraft->reference = $originalDraft->reference . '_copy_' . time();
        $newDraft->save();

        // Dupliquer les variants si ils existent
        foreach ($originalDraft->variants as $variant) {
            $newVariant = $variant->replicate();
            $newVariant->article_id = $newDraft->id;
            $newVariant->save();

            // Dupliquer les stocks et attributs si nécessaire...
        }

        return response()->json([
            'success' => true,
            'draft_id' => $newDraft->id,
            'message' => 'Brouillon dupliqué avec succès'
        ]);
    }
}
