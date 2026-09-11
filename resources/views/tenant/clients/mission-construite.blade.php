<x-tenant-app-layout>
@include('tenant.clients.partials.header-tabs', ['active' => 'analyse'])

{{-- $viewRole est déjà défini par le partial header-tabs inclus ci-dessus. --}}

<style>
.wd-mc-wrap{max-width:860px;margin:0 auto;padding:24px 0 60px;}
.wd-mc-back{display:inline-block;margin-bottom:16px;font-size:13px;color:var(--muted);text-decoration:none;}
.wd-mc-back:hover{color:var(--ink);}
.wd-mc-head{background:#fff;border:1px solid var(--line);border-radius:14px;padding:24px;margin-bottom:20px;}
.wd-mc-chips{display:flex;gap:8px;margin-bottom:10px;flex-wrap:wrap;}
.wd-mc-chip{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.03em;background:var(--soft);color:var(--muted);padding:4px 10px;border-radius:20px;}
.wd-mc-chip-statut{background:#e9f3f1;color:#3e7a6c;}
.wd-mc-chip-statut-echec{background:#fbe9e9;color:#b23b3b;}
.wd-mc-title{font-size:20px;font-weight:800;color:var(--ink);margin:0;}
.wd-mc-meta{font-size:12px;color:var(--muted);margin-top:6px;}

.wd-mc-error{background:#fbe9e9;border:1px solid #f0c4c4;color:#8a2f2f;border-radius:10px;padding:16px 18px;font-size:13px;margin-bottom:20px;}

.wd-mc-card{background:#fff;border:1px solid var(--line);border-radius:14px;padding:22px;margin-bottom:18px;}
.wd-mc-label{display:block;font-weight:800;font-size:13px;color:var(--ink);margin-bottom:6px;}
.wd-mc-hint{font-size:12px;color:var(--muted);margin:-2px 0 10px;}
.wd-mc-input,.wd-mc-textarea{width:100%;border:1px solid var(--line);border-radius:10px;padding:12px 14px;font-size:14px;font-family:inherit;color:var(--ink);background:#fff;box-sizing:border-box;}
.wd-mc-textarea{resize:vertical;line-height:1.5;}
.wd-mc-input:focus,.wd-mc-textarea:focus{outline:none;border-color:var(--pink);}

.wd-mc-pieces{list-style:none;margin:0;padding:0;}
.wd-mc-piece{display:flex;gap:10px;align-items:flex-start;padding:10px 0;border-top:1px solid var(--line);}
.wd-mc-piece:first-child{border-top:none;}
.wd-mc-piece-badge{flex:0 0 auto;font-size:10px;font-weight:800;text-transform:uppercase;padding:3px 8px;border-radius:20px;margin-top:2px;}
.wd-mc-piece-badge-oui{background:#fbe9e9;color:#b23b3b;}
.wd-mc-piece-badge-non{background:var(--soft);color:var(--muted);}
.wd-mc-piece-doc{font-weight:700;font-size:13px;color:var(--ink);}
.wd-mc-piece-raison{font-size:12px;color:var(--muted);margin-top:2px;}
.wd-mc-pieces-empty{font-size:13px;color:var(--muted);}
.wd-mc-text{font-size:14px;color:var(--ink);line-height:1.6;margin:0;white-space:pre-line;}
.wd-mc-readonly-list{margin:0;padding-left:18px;font-size:14px;color:var(--ink);line-height:1.7;}

.wd-mc-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:20px;}
.wd-mc-save{background:#242424;color:#fff;border:none;border-radius:8px;padding:12px 22px;font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.04em;cursor:pointer;}
.wd-mc-save:hover{background:var(--pink);}
</style>

<div class="wd-mc-wrap">

    <a href="{{ route('tenant.clients.aide-decision', $client) }}" class="wd-mc-back">
        ← Retour à l'aide à la décision
    </a>

    @if(session('status'))
        <div class="wd-mc-card" style="border-color:#bfe0d6;background:#f3faf8;color:#2f6b5a;">
            {{ session('status') }}
        </div>
    @endif

    <div class="wd-mc-head">

        <div class="wd-mc-chips">
            @if($mission->categorie)
                <span class="wd-mc-chip">{{ $mission->categorie }}</span>
            @endif
            @if($mission->type_document)
                <span class="wd-mc-chip">{{ $mission->type_document }}</span>
            @endif

            @if($mission->status === 'completed')
                <span class="wd-mc-chip wd-mc-chip-statut">
                    {{ $mission->valide_le ? 'Validée par le conseiller' : 'À relire' }}
                </span>
            @elseif($mission->status === 'failed')
                <span class="wd-mc-chip wd-mc-chip-statut-echec">Échec de construction</span>
            @else
                <span class="wd-mc-chip">En cours de construction</span>
            @endif
        </div>

        <h1 class="wd-mc-title">
            {{ $mission->contenuAffiche()['intitule_mission'] ?? ($mission->prestation_snapshot['titre'] ?? 'Mission') }}
        </h1>

        <p class="wd-mc-meta">
            Construite le {{ $mission->created_at?->copy()->setTimezone('Europe/Paris')->translatedFormat('d F Y à H:i') }}
            @if($mission->valide_le)
                · Enregistrée le {{ $mission->valide_le->copy()->setTimezone('Europe/Paris')->translatedFormat('d F Y à H:i') }}
            @endif
        </p>

    </div>

    @if($mission->status === 'failed')

        <div class="wd-mc-error">
            La construction de cette mission a échoué : {{ $mission->error_message ?? 'erreur inconnue.' }}
            <br>
            Retourne sur l'aide à la décision et sélectionne de nouveau la prestation pour relancer la construction.
        </div>

    @elseif($mission->status !== 'completed')

        <div class="wd-mc-card">
            Construction en cours. Recharge la page dans quelques instants.
        </div>

    @else

        @php
            $contenu = $mission->contenuAffiche() ?? [];

            $versTexte = fn ($liste) => is_array($liste)
                ? implode("\n", $liste)
                : '';
        @endphp

        @if($viewRole !== 'client')

        <form method="POST" action="{{ route('tenant.clients.missions.update', ['client' => $client, 'mission' => $mission]) }}">
            @csrf

            <div class="wd-mc-card">
                <label class="wd-mc-label" for="intitule_mission">Intitulé de la mission</label>
                <input
                    type="text"
                    id="intitule_mission"
                    name="intitule_mission"
                    class="wd-mc-input"
                    value="{{ old('intitule_mission', $contenu['intitule_mission'] ?? '') }}"
                    required
                >
            </div>

            <div class="wd-mc-card">
                <label class="wd-mc-label" for="contexte">Contexte identifié</label>
                <p class="wd-mc-hint">Ce qui, dans le dossier, justifie cette mission.</p>
                <textarea
                    id="contexte"
                    name="contexte"
                    class="wd-mc-textarea"
                    rows="5"
                    required
                >{{ old('contexte', $contenu['contexte'] ?? '') }}</textarea>
            </div>

            <div class="wd-mc-card">
                <label class="wd-mc-label" for="objet">Objet de la mission</label>
                <p class="wd-mc-hint">Ce que le client confie précisément au cabinet.</p>
                <textarea
                    id="objet"
                    name="objet"
                    class="wd-mc-textarea"
                    rows="4"
                    required
                >{{ old('objet', $contenu['objet'] ?? '') }}</textarea>
            </div>

            <div class="wd-mc-card">
                <label class="wd-mc-label" for="perimetre">Périmètre</label>
                <p class="wd-mc-hint">Un élément par ligne. Ajoute, modifie ou supprime des lignes librement.</p>
                <textarea
                    id="perimetre"
                    name="perimetre"
                    class="wd-mc-textarea"
                    rows="6"
                    required
                >{{ old('perimetre', $versTexte($contenu['perimetre'] ?? [])) }}</textarea>
            </div>

            <div class="wd-mc-card">
                <label class="wd-mc-label" for="hors_perimetre">Hors périmètre (exclusions)</label>
                <p class="wd-mc-hint">Un élément par ligne : ce que cette mission ne couvre pas.</p>
                <textarea
                    id="hors_perimetre"
                    name="hors_perimetre"
                    class="wd-mc-textarea"
                    rows="4"
                    required
                >{{ old('hors_perimetre', $versTexte($contenu['hors_perimetre'] ?? [])) }}</textarea>
            </div>

            <div class="wd-mc-card">
                <label class="wd-mc-label" for="travaux">Travaux prévus</label>
                <p class="wd-mc-hint">Un élément par ligne, dans l'ordre d'exécution.</p>
                <textarea
                    id="travaux"
                    name="travaux"
                    class="wd-mc-textarea"
                    rows="6"
                    required
                >{{ old('travaux', $versTexte($contenu['travaux'] ?? [])) }}</textarea>
            </div>

            <div class="wd-mc-card">
                <label class="wd-mc-label" for="livrables">Livrables</label>
                <p class="wd-mc-hint">Un élément par ligne.</p>
                <textarea
                    id="livrables"
                    name="livrables"
                    class="wd-mc-textarea"
                    rows="4"
                    required
                >{{ old('livrables', $versTexte($contenu['livrables'] ?? [])) }}</textarea>
            </div>

            <div class="wd-mc-card">
                <span class="wd-mc-label">Pièces à collecter</span>
                <p class="wd-mc-hint">Généré automatiquement, non modifiable ici pour l'instant.</p>

                @if(! empty($contenu['pieces_a_collecter']))
                    <ul class="wd-mc-pieces">
                        @foreach($contenu['pieces_a_collecter'] as $piece)
                            <li class="wd-mc-piece">
                                <span class="wd-mc-piece-badge {{ ! empty($piece['necessaire']) ? 'wd-mc-piece-badge-oui' : 'wd-mc-piece-badge-non' }}">
                                    {{ ! empty($piece['necessaire']) ? 'Indispensable' : 'Utile' }}
                                </span>
                                <div>
                                    <div class="wd-mc-piece-doc">{{ $piece['document'] ?? '' }}</div>
                                    @if(! empty($piece['raison']))
                                        <div class="wd-mc-piece-raison">{{ $piece['raison'] }}</div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="wd-mc-pieces-empty">Aucune pièce complémentaire identifiée.</p>
                @endif
            </div>

            <div class="wd-mc-actions">
                <button type="submit" class="wd-mc-save">Enregistrer</button>
            </div>

        </form>


        @else

            <div class="wd-mc-card">
                <span class="wd-mc-label">Intitulé de la mission</span>
                <p class="wd-mc-text">{{ $contenu['intitule_mission'] ?? '' }}</p>
            </div>

            <div class="wd-mc-card">
                <span class="wd-mc-label">Contexte identifié</span>
                <p class="wd-mc-text">{{ $contenu['contexte'] ?? '' }}</p>
            </div>

            <div class="wd-mc-card">
                <span class="wd-mc-label">Objet de la mission</span>
                <p class="wd-mc-text">{{ $contenu['objet'] ?? '' }}</p>
            </div>

            <div class="wd-mc-card">
                <span class="wd-mc-label">Périmètre</span>
                <ul class="wd-mc-readonly-list">
                    @foreach($contenu['perimetre'] ?? [] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="wd-mc-card">
                <span class="wd-mc-label">Hors périmètre</span>
                <ul class="wd-mc-readonly-list">
                    @foreach($contenu['hors_perimetre'] ?? [] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="wd-mc-card">
                <span class="wd-mc-label">Travaux prévus</span>
                <ul class="wd-mc-readonly-list">
                    @foreach($contenu['travaux'] ?? [] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="wd-mc-card">
                <span class="wd-mc-label">Livrables</span>
                <ul class="wd-mc-readonly-list">
                    @foreach($contenu['livrables'] ?? [] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="wd-mc-card">
                <span class="wd-mc-label">Pièces à collecter</span>
                <p class="wd-mc-hint">Généré automatiquement, non modifiable ici pour l'instant.</p>

                @if(! empty($contenu['pieces_a_collecter']))
                    <ul class="wd-mc-pieces">
                        @foreach($contenu['pieces_a_collecter'] as $piece)
                            <li class="wd-mc-piece">
                                <span class="wd-mc-piece-badge {{ ! empty($piece['necessaire']) ? 'wd-mc-piece-badge-oui' : 'wd-mc-piece-badge-non' }}">
                                    {{ ! empty($piece['necessaire']) ? 'Indispensable' : 'Utile' }}
                                </span>
                                <div>
                                    <div class="wd-mc-piece-doc">{{ $piece['document'] ?? '' }}</div>
                                    @if(! empty($piece['raison']))
                                        <div class="wd-mc-piece-raison">{{ $piece['raison'] }}</div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="wd-mc-pieces-empty">Aucune pièce complémentaire identifiée.</p>
                @endif
            </div>

        @endif

    @endif

</div>

</x-tenant-app-layout>
