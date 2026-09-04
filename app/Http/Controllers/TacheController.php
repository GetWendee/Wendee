<?php
declare(strict_types=1);
namespace App\Http\Controllers;
use App\Models\Tache;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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
            'aFaire' => Tache::where('fait', false)->latest()->get(),
            'faites' => Tache::where('fait', true)->latest()->get(),
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
}
