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
                <a href="{{ route('tenant.clients.documents.download', [$client, $cle]) }}" title="Télécharger" aria-label="Télécharger">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg>
                </a>
                <form method="POST" action="{{ route('tenant.clients.documents.destroy', [$client, $cle]) }}" onsubmit="return confirm('Supprimer ce document ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" title="Supprimer" aria-label="Supprimer">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                    </button>
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
