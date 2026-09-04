<x-tenant-app-layout>
@include('tenant.clients.partials.header-tabs', ['active' => 'archives'])

@if (session('status_simple'))
    <div style="background:#e9f4ec;border:1px solid #cfe6d6;color:#2f5d3f;border-radius:9px;padding:12px 16px;font-size:13px;margin-bottom:6px;">
        {{ session('status_simple') }}
    </div>
@endif

<style>
.wd-section{margin-top:22px;scroll-margin-top:88px;}
.wd-section-head{display:flex;justify-content:space-between;align-items:end;margin-bottom:11px;}
.wd-section-head h2{margin:4px 0 0;font-size:21px;letter-spacing:-.03em;}
.wd-panel{background:white;border:1px solid var(--line);border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(21,21,21,.04);}

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
    transition:background .15s,color .15s,border-color .15s;
}
.wd-archives-pill:hover{border-color:var(--pink);color:var(--ink);}
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
    padding:14px 22px;
    border-bottom:1px solid var(--line);
    background:#faf9f7;
}
.wd-archives-table tbody td{
    padding:16px 22px;
    border-bottom:1px solid var(--line);
    font-size:13px;
    color:var(--ink);
    vertical-align:middle;
}
.wd-archives-table tbody tr:last-child td{
    border-bottom:none;
}
.wd-archives-table tbody tr:hover td{
    background:#faf9f7;
}
.wd-archives-nom{
    font-weight:650;
}
.wd-archives-tag{
    display:inline-block;
    font-size:10px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.04em;
    padding:3px 9px;
    border-radius:999px;
    margin-left:8px;
}
.wd-archives-tag.tag-mandats{color:var(--pink);background:#fdf2f8;}
.wd-archives-tag.tag-recommandations{color:#1b1716;background:#f0ede9;}
.wd-archives-tag.tag-plans-actions{color:var(--green);background:#e9f4ec;}
.wd-archives-date{
    color:var(--muted);
}
.wd-archives-btn{
    display:inline-flex;
    align-items:center;
    gap:6px;
    background:#242424;
    border-top:2px solid var(--pink);
    color:#fff;
    font-size:11px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.03em;
    padding:8px 16px;
    border-radius:7px;
    text-decoration:none;
}
.wd-archives-empty{
    padding:44px;
    text-align:center;
    color:var(--muted);
    font-size:13px;
}
.wd-docs-grid{
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:16px;
}
@media (max-width:900px){
    .wd-docs-grid{grid-template-columns:repeat(2,1fr);}
}
@media (max-width:560px){
    .wd-docs-grid{grid-template-columns:1fr;}
}
.wd-doc-card{
    background:#fff;
    border:1px solid var(--line);
    border-radius:12px;
    padding:20px;
    box-shadow:0 1px 3px rgba(21,21,21,.04);
    min-width:0;
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
    margin-bottom:16px;
}
.wd-doc-empty{
    display:flex;
    align-items:center;
    gap:10px;
    border:1px dashed var(--line);
    border-radius:9px;
    padding:14px 16px;
    font-size:12px;
    color:var(--muted);
}
.wd-doc-empty svg{flex:0 0 auto;color:#c7c1bb;}
.wd-doc-file{
    display:flex;
    flex-direction:column;
    gap:10px;
    border:1px solid var(--line);
    border-radius:9px;
    padding:12px 14px;
}
.wd-doc-file-nom{
    display:flex;
    align-items:center;
    gap:8px;
    font-size:12px;
    color:var(--ink);
    font-weight:600;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}
.wd-doc-file-nom svg{flex:0 0 auto;color:var(--pink);}
.wd-doc-file-actions{
    display:flex;
    gap:14px;
}
.wd-doc-file-actions a,.wd-doc-file-actions button{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:30px;
    height:30px;
    background:none;
    border:1px solid var(--line);
    border-radius:7px;
    cursor:pointer;
    text-decoration:none;
    font-family:inherit;
}
.wd-doc-file-actions a{color:var(--pink);}
.wd-doc-file-actions a:hover{background:#fdf2f8;border-color:var(--pink);}
.wd-doc-file-actions button{color:#b94d4d;}
.wd-doc-file-actions button:hover{background:#fdf0f0;border-color:#b94d4d;}
.wd-upload-overlay{
    position:fixed;
    inset:0;
    background:rgba(21,21,21,.45);
    display:flex;
    align-items:center;
    justify-content:center;
    z-index:3000;
}
.wd-upload-modal{
    background:#fff;
    border-radius:14px;
    width:420px;
    max-width:92vw;
    padding:26px;
}
.wd-upload-modal h3{
    margin:4px 0 18px;
    font-size:18px;
    letter-spacing:-.02em;
}
.wd-upload-field{margin-bottom:16px;}
.wd-upload-field label{
    display:block;
    font-size:11px;
    font-weight:700;
    color:var(--muted);
    text-transform:uppercase;
    letter-spacing:.04em;
    margin-bottom:6px;
}
.wd-upload-field select,.wd-upload-field input[type=file]{
    width:100%;
    border:1px solid var(--line);
    border-radius:8px;
    padding:9px 11px;
    font-size:13px;
    font-family:inherit;
}
.wd-upload-actions{
    display:flex;
    justify-content:flex-end;
    gap:10px;
    margin-top:6px;
}
.wd-upload-cancel{
    background:none;
    border:none;
    font-size:12px;
    font-weight:700;
    color:var(--muted);
    cursor:pointer;
    padding:10px 14px;
}

.wd-subtabs{
    display:flex;
    align-items:center;
    gap:22px;
    margin:0 0 28px;
    padding:16px 0 16px;
    border-bottom:1px solid var(--line);
}
.wd-subtabs-links{
    display:flex;
    align-items:center;
    gap:26px;
    padding-left:22px;
    border-left:1px solid var(--line);
}
.wd-subtabs a{
    position:relative;
    padding-bottom:5px;
    color:#817b76;
    font-size:10.5px;
    font-weight:800;
    letter-spacing:.08em;
    text-transform:uppercase;
    text-decoration:none;
}
.wd-subtabs a::after{
    content:'';
    position:absolute;
    left:0;
    right:0;
    bottom:0;
    height:2px;
    background:var(--pink);
    transform:scaleX(0);
    transform-origin:left;
    transition:transform .18s ease;
}
.wd-subtabs a:hover{color:#151515;}
.wd-subtabs a:hover::after{transform:scaleX(1);}
.wd-btn-dark{
    flex:0 0 auto;
    min-width:220px;
    min-height:40px;
    padding:0 20px;
    border:1px solid rgba(255,255,255,.10);
    border-top:2px solid #FF3399;
    border-radius:8px;
    background:#242424;
    color:#ffffff;
    font-size:9px;
    font-weight:800;
    letter-spacing:.10em;
    text-transform:uppercase;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    font-family:inherit;
}
.wd-toast{
    position:fixed;
    bottom:26px;
    right:26px;
    background:#242424;
    color:#fff;
    border-top:2px solid var(--pink);
    padding:12px 20px;
    border-radius:8px;
    font-size:12px;
    font-weight:600;
    z-index:2000;
}
</style>

<div x-data="{ uploadOpen: false }" x-cloak>
<nav class="wd-subtabs">
    <button type="button" class="wd-btn-dark" @click="uploadOpen = true">Ajouter un document</button>
    <div class="wd-subtabs-links">
        <a href="#bibliotheque">Bibliothèque</a>
        <a href="#documents-personnels">Documents personnels</a>
        <a href="#documents-mandats">Documents liés aux mandats</a>
    </div>
</nav>

<section class="wd-section" id="bibliotheque">
    <div class="wd-section-head">
        <div>
            <div class="wd-eyebrow">Documents générés</div>
            <h2>Bibliothèque</h2>
        </div>
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
                                    <span class="wd-archives-tag tag-{{ $document['categorie'] }}">{{ $document['categorie_label'] }}</span>
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

<section class="wd-section" id="documents-personnels">
    <div class="wd-section-head">
        <div>
            <div class="wd-eyebrow">KYC</div>
            <h2>Documents personnels</h2>
        </div>
    </div>
    <div class="wd-docs-grid">
        @foreach ($typesDocumentsPersonnels as $cle => $meta)
            @if ($meta['categorie'] === 'personnels')
                @include('tenant.clients.partials.document-card', ['cle' => $cle, 'meta' => $meta])
            @endif
        @endforeach
    </div>
</section>

<section class="wd-section" id="documents-mandats">
    <div class="wd-section-head">
        <div>
            <div class="wd-eyebrow">Mandats</div>
            <h2>Documents liés aux mandats</h2>
        </div>
    </div>
    <div class="wd-docs-grid">
        @foreach ($typesDocumentsPersonnels as $cle => $meta)
            @if ($meta['categorie'] === 'mandats')
                @include('tenant.clients.partials.document-card', ['cle' => $cle, 'meta' => $meta])
            @endif
        @endforeach
    </div>
</section>

<div class="wd-upload-overlay" x-show="uploadOpen" x-cloak @click.self="uploadOpen = false">
    <div class="wd-upload-modal">
        <div class="wd-eyebrow">Documents</div>
        <h3>Ajouter un document</h3>
        <form method="POST" action="{{ route('tenant.clients.documents.store', $client) }}" enctype="multipart/form-data">
            @csrf
            <div class="wd-upload-field">
                <label>Type de document</label>
                <select name="type" required>
                    <option value="" disabled selected>Choisir</option>
                    @foreach ($typesDocumentsPersonnels as $cle => $meta)
                        <option value="{{ $cle }}">{{ $meta['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="wd-upload-field">
                <label>Fichier (PDF, JPG, PNG — 10 Mo max)</label>
                <input type="file" name="fichier" accept=".pdf,.jpg,.jpeg,.png" required>
            </div>
            @error('fichier')
                <p style="color:#b94d4d;font-size:12px;margin:-8px 0 14px;">{{ $message }}</p>
            @enderror
            <div class="wd-upload-actions">
                <button type="button" class="wd-upload-cancel" @click="uploadOpen = false">Annuler</button>
                <button type="submit" class="wd-btn-dark" style="min-width:auto;">Envoyer</button>
            </div>
        </form>
    </div>
</div>

</div>

</x-tenant-app-layout>
