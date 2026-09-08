<?php
declare(strict_types=1);
namespace App\Http\Controllers;
use App\Models\Tache;
use App\Models\TachePieceJointe;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
class TacheController extends Controller
{
    public const MODULES = [
        'Dashboard',
        'CRM Clients',
        'Analyse patrimoniale',
        'Aide à la décision',
        'Analyse financière',
        'Offres / solutions',
        'Documents',
        'Marché',
        'Cabinet / Console',
    ];
    public function index(): View
    {
        return view('a-faire.index', [
            'aFaire' => Tache::with('piecesJointes')->where('fait', false)->latest()->get(),
            'faites' => Tache::with('piecesJointes')->where('fait', true)->latest()->get(),
            'modules' => self::MODULES,
        ]);
    }
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'page_module' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
        Tache::create($validated);
        return redirect()->route('a-faire.index')->with('status_simple', 'Tâche ajoutée.');
    }
    public function update(Request $request, Tache $tache): RedirectResponse
    {
        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'page_module' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
        $tache->update($validated);
        return redirect()->route('a-faire.index')->with('status_simple', 'Tâche mise à jour.');
    }
    public function toggleFait(Tache $tache): RedirectResponse
    {
        $tache->update(['fait' => ! $tache->fait]);
        return redirect()->route('a-faire.index')->with(
            'status_simple',
            $tache->fait ? 'Tâche marquée comme faite.' : 'Tâche réouverte.'
        );
    }
    public function destroy(Tache $tache): RedirectResponse
    {
        $tache->delete();
        return redirect()->route('a-faire.index')->with('status_simple', 'Tâche supprimée.');
    }
    public function storePieceJointe(Request $request, Tache $tache): RedirectResponse
    {
        $validated = $request->validate([
            'fichier' => ['required', 'file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,txt'],
        ]);
        $fichier = $validated['fichier'];
        $path = $fichier->store('taches/' . $tache->id, 'local');
        TachePieceJointe::create([
            'tache_id' => $tache->id,
            'fichier_path' => $path,
            'nom_original' => $fichier->getClientOriginalName(),
            'mime_type' => $fichier->getMimeType(),
            'taille' => $fichier->getSize(),
        ]);
        return redirect()->route('a-faire.index')->with('status_simple', 'Pièce jointe ajoutée.');
    }
    public function showPieceJointe(TachePieceJointe $pieceJointe): \Symfony\Component\HttpFoundation\Response
    {
        abort_unless(Storage::disk('local')->exists($pieceJointe->fichier_path), 404);
        return Storage::disk('local')->response($pieceJointe->fichier_path, $pieceJointe->nom_original);
    }
    public function destroyPieceJointe(TachePieceJointe $pieceJointe): RedirectResponse
    {
        Storage::disk('local')->delete($pieceJointe->fichier_path);
        $pieceJointe->delete();
        return redirect()->route('a-faire.index')->with('status_simple', 'Pièce jointe supprimée.');
    }
}
