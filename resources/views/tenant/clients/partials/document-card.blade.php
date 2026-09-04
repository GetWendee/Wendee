<div class="wd-doc-card">
    <div class="wd-doc-titre">{{ $meta['label'] }}</div>
    <div class="wd-doc-sub">{{ $meta['sub'] }}</div>
    @if ($fichiersPersonnels->has($cle))
        <div class="wd-doc-file">
            <div class="wd-doc-file-nom">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                <span>{{ $fichiersPersonnels[$cle]->nom_original }}</span>
            </div>
            <div class="wd-doc-file-actions">
                <a href="{{ route('tenant.clients.documents.download', [$client, $cle]) }}">Télécharger</a>
                <form method="POST" action="{{ route('tenant.clients.documents.destroy', [$client, $cle]) }}" onsubmit="return confirm('Supprimer ce document ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Supprimer</button>
                </form>
            </div>
        </div>
    @else
        <div class="wd-doc-empty">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
            Aucun document
        </div>
    @endif
</div>
