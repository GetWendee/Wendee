<x-tenant-app-layout>
@include('tenant.clients.partials.header-tabs', ['active' => 'archives'])

<style>
.wd-archives-pills{
    display:flex;
    flex-wrap:wrap;
    gap:8px;
    margin-bottom:16px;
}
.wd-archives-pill{
    border:1px solid var(--line);
    background:#fff;
    color:var(--muted);
    font-size:12px;
    font-weight:700;
    padding:8px 16px;
    border-radius:999px;
    cursor:pointer;
}
.wd-archives-pill.active{
    background:var(--pink);
    border-color:var(--pink);
    color:#fff;
}
.wd-archives-table{
    width:100%;
    border-collapse:collapse;
}
.wd-archives-table thead th{
    text-align:left;
    font-size:10px;
    font-weight:700;
    letter-spacing:.06em;
    text-transform:uppercase;
    color:var(--muted);
    padding:12px 20px;
    border-bottom:1px solid var(--line);
}
.wd-archives-table tbody td{
    padding:14px 20px;
    border-bottom:1px solid var(--line);
    font-size:13px;
    color:var(--ink);
    vertical-align:middle;
}
.wd-archives-table tbody tr:last-child td{
    border-bottom:none;
}
.wd-archives-nom{
    font-weight:600;
}
.wd-archives-tag{
    display:inline-block;
    font-size:10px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.04em;
    color:var(--pink);
    background:#fdf2f8;
    padding:3px 9px;
    border-radius:999px;
    margin-left:8px;
}
.wd-archives-date{
    color:var(--muted);
}
.wd-archives-btn{
    display:inline-flex;
    align-items:center;
    background:var(--pink);
    color:#fff;
    font-size:11px;
    font-weight:700;
    padding:7px 14px;
    border-radius:7px;
    text-decoration:none;
}
.wd-archives-empty{
    padding:40px;
    text-align:center;
    color:var(--muted);
    font-size:13px;
}
.wd-docs-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
    gap:16px;
}
.wd-doc-card{
    background:#fff;
    border:1px solid var(--line);
    border-radius:12px;
    padding:18px;
}
.wd-doc-titre{
    font-size:14px;
    font-weight:700;
    color:var(--ink);
}
.wd-doc-sub{
    font-size:12px;
    color:var(--muted);
    margin-top:2px;
    margin-bottom:14px;
}
.wd-doc-empty{
    border:1px dashed var(--line);
    border-radius:9px;
    padding:16px;
    text-align:center;
    font-size:12px;
    color:var(--muted);
}
</style>

<section class="wd-section">
    <div class="wd-section-head">
        <h2>Bibliothèque</h2>
    </div>

    <div x-data="{ filtre: 'tous' }">
        <div class="wd-archives-pills">
            <button type="button" class="wd-archives-pill" :class="{ active: filtre === 'tous' }" @click="filtre = 'tous'">Tous</button>
            <button type="button" class="wd-archives-pill" :class="{ active: filtre === 'mandats' }" @click="filtre = 'mandats'">Mandats</button>
            <button type="button" class="wd-archives-pill" :class="{ active: filtre === 'recommandations' }" @click="filtre = 'recommandations'">Recommandations</button>
            <button type="button" class="wd-archives-pill" :class="{ active: filtre === 'plans-actions' }" @click="filtre = 'plans-actions'">Plans actions</button>
        </div>

        <div class="wd-panel">
            @if ($documents->isEmpty())
                <div class="wd-archives-empty">Aucun document généré pour ce client pour le moment.</div>
            @else
                <table class="wd-archives-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Date de modification</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documents as $document)
                            <tr x-show="filtre === 'tous' || filtre === '{{ $document['categorie'] }}'">
                                <td>
                                    <span class="wd-archives-nom">{{ $document['label'] }}</span>
                                    <span class="wd-archives-tag">{{ $document['categorie_label'] }}</span>
                                </td>
                                <td class="wd-archives-date">{{ $document['date']->translatedFormat('d M. Y') }}</td>
                                <td style="text-align:right;">
                                    <a href="{{ $document['url'] }}" class="wd-archives-btn">Télécharger</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</section>

<section class="wd-section">
    <div class="wd-section-head">
        <h2>Documents personnels</h2>
    </div>
    <div class="wd-docs-grid">
        <div class="wd-doc-card">
            <div class="wd-doc-titre">Mes pièces d'identité</div>
            <div class="wd-doc-sub">Carte d'identité, passeport, carte de résident</div>
            <div class="wd-doc-empty">Aucun document</div>
        </div>
        <div class="wd-doc-card">
            <div class="wd-doc-titre">Mes justificatifs de domicile</div>
            <div class="wd-doc-sub">Documents liés à votre adresse</div>
            <div class="wd-doc-empty">Aucun document</div>
        </div>
        <div class="wd-doc-card">
            <div class="wd-doc-titre">Avis d'imposition</div>
            <div class="wd-doc-sub">Dernier document fiscal sur vos revenus</div>
            <div class="wd-doc-empty">Aucun document</div>
        </div>
        <div class="wd-doc-card">
            <div class="wd-doc-titre">Avis IFI</div>
            <div class="wd-doc-sub">Document fiscal de votre patrimoine immobilier</div>
            <div class="wd-doc-empty">Aucun document</div>
        </div>
    </div>
</section>

<section class="wd-section">
    <div class="wd-section-head">
        <h2>Documents liés aux mandats</h2>
    </div>
    <div class="wd-docs-grid">
        <div class="wd-doc-card">
            <div class="wd-doc-titre">Permis de conduire</div>
            <div class="wd-doc-sub">Assurance véhicule</div>
            <div class="wd-doc-empty">Aucun document</div>
        </div>
        <div class="wd-doc-card">
            <div class="wd-doc-titre">Cartes grises</div>
            <div class="wd-doc-sub">Assurance véhicule</div>
            <div class="wd-doc-empty">Aucun document</div>
        </div>
        <div class="wd-doc-card">
            <div class="wd-doc-titre">Relevés d'information</div>
            <div class="wd-doc-sub">Assurance véhicule</div>
            <div class="wd-doc-empty">Aucun document</div>
        </div>
        <div class="wd-doc-card">
            <div class="wd-doc-titre">Relevés d'information</div>
            <div class="wd-doc-sub">Assurance habitation</div>
            <div class="wd-doc-empty">Aucun document</div>
        </div>
    </div>
</section>

</x-tenant-app-layout>
