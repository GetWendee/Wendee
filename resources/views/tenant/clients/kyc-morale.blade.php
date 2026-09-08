@php
    $kyc = $client->kycMorale;
    $old = fn($field, $default = null) => old($field, $kyc?->{$field} ?? $default);
    $optionsListe = function($liste, $field, $valeurActuelle = null) use ($old) {
        $current = $valeurActuelle ?? $old($field);
        $html = '<option value="">-</option>';
        foreach (($liste ?? []) as $value => $label) {
            $sel = ((string) $current === (string) $value) ? 'selected' : '';
            $html .= '<option value="'.e($value).'" '.$sel.'>'.e($label).'</option>';
        }
        return $html;
    };
    $dirigeants = old('dirigeants', $client->intervenants->where('type_intervenant', 'dirigeant')->map(fn ($i) => ['nom' => $i->nom, 'role' => $i->role])->values()->all());
    $actionnaires = old('actionnaires', $client->intervenants->where('type_intervenant', 'actionnaire')->map(fn ($i) => ['nom' => $i->nom, 'pourcentage_detention' => $i->pourcentage_detention])->values()->all());
    $beneficiaires = old('beneficiaires_effectifs', $client->intervenants->where('type_intervenant', 'beneficiaire_effectif')->map(fn ($i) => ['nom' => $i->nom, 'role' => $i->role])->values()->all());
    $lignesPostes = fn ($field) => old($field, $kyc?->{$field} ?? []);
@endphp
<x-tenant-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Recueil de connaissance société - {{ $client->nomAffichage() }}
            </h2>
            <a href="{{ route('tenant.clients.show', $client) }}" class="text-sm text-gray-600 underline">
                {{ __('Retour à la fiche client') }}
            </a>
        </div>
    </x-slot>

<style>
.wd-wrap{max-width:1000px;margin:auto;padding:30px 34px 60px}
.wd-user-form{margin-top:14px;padding:23px;background:#fff;border:1px solid #ded9d4;border-radius:10px}
.wd-cabinet-information-grid{display:grid;grid-template-columns:repeat(6, 1fr);column-gap:20px;margin-top:6px;border-top:1px solid #eeeae7}
.wd-cabinet-field{position:relative;padding:16px 0 14px 0;border-bottom:1px solid #eeeae7;grid-column:span 6}
.wd-cabinet-field.wd-c2{grid-column:span 2}
.wd-cabinet-field.wd-c3{grid-column:span 3}
.wd-cabinet-field.wd-c4{grid-column:span 4}
.wd-cabinet-field label{display:block;color:#9a928d;font-size:8px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;margin-bottom:8px}
.wd-cabinet-field input[type=text],.wd-cabinet-field input[type=number],.wd-cabinet-field input[type=date],.wd-cabinet-field select,.wd-cabinet-field textarea{width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px;color:#242424;background:#fff;font-family:inherit}
.wd-cabinet-field input:focus,.wd-cabinet-field select:focus,.wd-cabinet-field textarea:focus{outline:none;border-color:#f40087}
.wd-section-title{margin:26px 0 4px;font-size:11px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#9a928d}
.wd-repeater-row{display:grid;grid-template-columns:1fr 1fr auto;gap:10px;align-items:end;padding:10px 0;border-bottom:1px solid #eeeae7}
.wd-repeater-row.wd-cols-5{grid-template-columns:1.4fr .8fr .8fr .8fr 1.4fr auto}
.wd-repeater-remove{border:1px solid #ded9d4;border-radius:7px;background:#fff;color:#b94d4d;font-size:11px;font-weight:800;padding:9px 12px;cursor:pointer}
.wd-repeater-add{margin-top:10px;border:1px dashed #ded9d4;border-radius:7px;background:#fff;color:#242424;font-size:12px;font-weight:700;padding:9px 14px;cursor:pointer}
.wd-cabinet-save{margin-top:20px;display:inline-flex;align-items:center;gap:10px;padding:11px 18px;border-radius:7px;background:#242424;color:#fff;border:0;font-size:11px;font-weight:800;letter-spacing:.02em;cursor:pointer;font-family:inherit}
.wd-checkbox-line{display:flex;align-items:center;gap:8px;font-size:12px;color:#242424}
</style>

<div class="wd-wrap">
    @if (session('status'))
        <div style="margin-bottom:14px;padding:12px 16px;border-radius:8px;background:#eef7f0;color:#2f6b45;font-size:13px">{{ session('status') }}</div>
    @endif

    <section class="wd-user-form">
        <form method="POST" action="{{ route('tenant.clients.kyc-morale.update', $client) }}">
            @csrf
            @method('PUT')

            <div class="wd-section-title">Éléments généraux</div>
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field wd-c3"><label>Nom / raison sociale</label><input type="text" value="{{ $client->nomAffichage() }}" disabled></div>
                <div class="wd-cabinet-field wd-c3"><label>Forme juridique</label><input type="text" value="{{ $client->forme_juridique }}" disabled></div>
                <div class="wd-cabinet-field wd-c3"><label>Numéro d'immatriculation</label><input type="text" value="{{ $client->numero_immatriculation }}" disabled></div>
                <div class="wd-cabinet-field wd-c3"><label>Régime fiscal</label><input type="text" value="{{ $client->regime_fiscal }}" disabled></div>
                <div class="wd-cabinet-field"><label>Adresse siège social</label><input type="text" value="{{ $client->adresse_siege_social }}" disabled></div>
            </div>
            <p style="font-size:11px;color:#9a928d;margin-top:4px">Ces champs se modifient depuis la fiche client, pas ici.</p>

            <div class="wd-section-title">Dirigeants</div>
            <div x-data="wdRepeater(@js($dirigeants))">
                <template x-for="(ligne, i) in items" :key="i">
                    <div class="wd-repeater-row">
                        <div><label style="display:block;color:#9a928d;font-size:8px;font-weight:800;text-transform:uppercase;margin-bottom:6px">Nom</label>
                            <input type="text" :name="'dirigeants['+i+'][nom]'" x-model="ligne.nom" style="width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px">
                        </div>
                        <div><label style="display:block;color:#9a928d;font-size:8px;font-weight:800;text-transform:uppercase;margin-bottom:6px">Rôle</label>
                            <input type="text" :name="'dirigeants['+i+'][role]'" x-model="ligne.role" style="width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px">
                        </div>
                        <button type="button" class="wd-repeater-remove" @click="retirer(i)">Retirer</button>
                    </div>
                </template>
                <button type="button" class="wd-repeater-add" @click="ajouter()">+ Ajouter un dirigeant</button>
            </div>

            <div class="wd-section-title">Actionnaires</div>
            <div x-data="wdRepeater(@js($actionnaires))">
                <template x-for="(ligne, i) in items" :key="i">
                    <div class="wd-repeater-row">
                        <div><label style="display:block;color:#9a928d;font-size:8px;font-weight:800;text-transform:uppercase;margin-bottom:6px">Nom</label>
                            <input type="text" :name="'actionnaires['+i+'][nom]'" x-model="ligne.nom" style="width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px">
                        </div>
                        <div><label style="display:block;color:#9a928d;font-size:8px;font-weight:800;text-transform:uppercase;margin-bottom:6px">% détention (direct/indirect)</label>
                            <input type="number" step="0.01" min="0" max="100" :name="'actionnaires['+i+'][pourcentage_detention]'" x-model="ligne.pourcentage_detention" style="width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px">
                        </div>
                        <button type="button" class="wd-repeater-remove" @click="retirer(i)">Retirer</button>
                    </div>
                </template>
                <button type="button" class="wd-repeater-add" @click="ajouter()">+ Ajouter un actionnaire</button>
            </div>

            <div class="wd-section-title">Bénéficiaires effectifs (sens LCB-FT)</div>
            <div x-data="wdRepeater(@js($beneficiaires))">
                <template x-for="(ligne, i) in items" :key="i">
                    <div class="wd-repeater-row">
                        <div><label style="display:block;color:#9a928d;font-size:8px;font-weight:800;text-transform:uppercase;margin-bottom:6px">Nom</label>
                            <input type="text" :name="'beneficiaires_effectifs['+i+'][nom]'" x-model="ligne.nom" style="width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px">
                        </div>
                        <div><label style="display:block;color:#9a928d;font-size:8px;font-weight:800;text-transform:uppercase;margin-bottom:6px">Rôle</label>
                            <input type="text" :name="'beneficiaires_effectifs['+i+'][role]'" x-model="ligne.role" style="width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px">
                        </div>
                        <button type="button" class="wd-repeater-remove" @click="retirer(i)">Retirer</button>
                    </div>
                </template>
                <button type="button" class="wd-repeater-add" @click="ajouter()">+ Ajouter un bénéficiaire effectif</button>
            </div>

            <div class="wd-section-title">Chiffres clés</div>
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field wd-c2"><label>Masse salariale mini</label><input type="number" name="masse_salariale_min" value="{{ $old('masse_salariale_min') }}"></div>
                <div class="wd-cabinet-field wd-c2"><label>Masse salariale moyenne</label><input type="number" name="masse_salariale_moyenne" value="{{ $old('masse_salariale_moyenne') }}"></div>
                <div class="wd-cabinet-field wd-c2"><label>Masse salariale maxi</label><input type="number" name="masse_salariale_max" value="{{ $old('masse_salariale_max') }}"></div>
                <div class="wd-cabinet-field wd-c3"><label>Valeur estimée de l'entreprise (€)</label><input type="number" step="0.01" name="valeur_estimee_entreprise" value="{{ $old('valeur_estimee_entreprise') }}"></div>
                <div class="wd-cabinet-field"><label>Éléments statutaires notables</label><textarea rows="2" name="elements_statutaires_notables">{{ $old('elements_statutaires_notables') }}</textarea></div>
                <div class="wd-cabinet-field"><label>Autres remarques notables</label><textarea rows="2" name="autres_remarques_notables">{{ $old('autres_remarques_notables') }}</textarea></div>
            </div>

            <div class="wd-section-title">Classification et connaissances</div>
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field wd-c3"><label>Classification MIF2</label><select name="classification_mif">{!! $optionsListe($listes['classification_mif'] ?? [], 'classification_mif') !!}</select></div>
                <div class="wd-cabinet-field wd-c3"><label>Connaissances financières</label><select name="connaissances_financieres">{!! $optionsListe($listes['connaissance_niveau'] ?? [], 'connaissances_financieres') !!}</select></div>
                <div class="wd-cabinet-field wd-c3"><label>Connaissances juridiques (facultatif)</label><select name="connaissances_juridiques">{!! $optionsListe($listes['connaissance_niveau'] ?? [], 'connaissances_juridiques') !!}</select></div>
            </div>

            <div class="wd-section-title">Pratique / détention de produits de placement</div>
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field wd-c3"><label class="wd-checkbox-line" style="text-transform:none;font-size:12px;color:#242424"><input type="checkbox" name="detient_produits_actuellement" value="1" {{ $old('detient_produits_actuellement') ? 'checked' : '' }}> Détention actuelle</label></div>
                <div class="wd-cabinet-field wd-c3"><label>Précisions</label><input type="text" name="detient_produits_actuellement_detail" value="{{ $old('detient_produits_actuellement_detail') }}"></div>
                <div class="wd-cabinet-field wd-c3"><label class="wd-checkbox-line" style="text-transform:none;font-size:12px;color:#242424"><input type="checkbox" name="a_detenu_produits_passe" value="1" {{ $old('a_detenu_produits_passe') ? 'checked' : '' }}> Détention passée</label></div>
                <div class="wd-cabinet-field wd-c3"><label>Précisions (dates si possible)</label><input type="text" name="a_detenu_produits_passe_detail" value="{{ $old('a_detenu_produits_passe_detail') }}"></div>
                <div class="wd-cabinet-field wd-c3"><label class="wd-checkbox-line" style="text-transform:none;font-size:12px;color:#242424"><input type="checkbox" name="supports_opcvm" value="1" {{ $old('supports_opcvm') ? 'checked' : '' }}> Supports OPCVM</label></div>
                <div class="wd-cabinet-field wd-c3"><label>Classe d'actif si OPCVM</label><input type="text" name="supports_opcvm_classe_actif" value="{{ $old('supports_opcvm_classe_actif') }}"></div>
                <div class="wd-cabinet-field wd-c3"><label class="wd-checkbox-line" style="text-transform:none;font-size:12px;color:#242424"><input type="checkbox" name="produits_couverture" value="1" {{ $old('produits_couverture') ? 'checked' : '' }}> Produits de couverture</label></div>
                <div class="wd-cabinet-field wd-c3"><label>Autres</label><input type="text" name="autres_supports" value="{{ $old('autres_supports') }}"></div>
            </div>

            <div class="wd-section-title">Personne politiquement exposée (PPE)</div>
            <p style="font-size:11px;color:#9a928d">Pour le dirigeant exécutif et pour le contact suivi de dossier, l'un des cas ci-dessous s'applique-t-il, ou s'est-il appliqué dans les 3 dernières années ?</p>
            @php
                $categoriesPpe = [
                    'chef_etat_gouvernement' => "Chef de l'Etat, de Gouvernement, membre d'un gouvernement national ou de la commission européenne, parlementaire, organe exécutif d'un parti",
                    'conseil_constitutionnel' => "Membre du Conseil Constitutionnel, Conseil d'Etat, Cour de Cassation, magistrat de la Cour des Comptes",
                    'cour_supreme' => "Membre d'une cour suprême, constitutionnelle ou d'une autre juridiction dont les décisions ne sont pas, sauf circonstances exceptionnelles, susceptibles de recours",
                    'cour_comptes_banque_centrale' => "Membre d'une cour des comptes (magistrats) ou dirigeant/membre de l'organe de direction d'une banque centrale",
                    'ambassadeur' => "Ambassadeur ou chargé d'affaires (service de l'Etat à l'étranger)",
                    'officier_general' => "Officier général ou supérieur assurant le commandement d'une armée",
                    'entreprise_publique' => "Dirigeant ou membre d'un conseil d'administration, de surveillance ou directoire d'une entreprise publique",
                    'organisation_internationale' => "Dirigeant / Directeur / Directeur adjoint ou membre d'un conseil d'une organisation internationale créée par un traité",
                    'fonction_communautaire' => "Dirigeant ou fonction comparable au niveau communautaire ou international",
                ];
                $ppeReponses = old('ppe_reponses', $kyc?->ppe_reponses ?? []);
            @endphp
            <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse;font-size:12px;margin-top:10px">
                <thead>
                    <tr style="text-align:left;color:#9a928d;font-size:10px;text-transform:uppercase">
                        <th style="padding:8px 6px">Catégorie</th>
                        <th style="padding:8px 6px">Dirigeant exécutif</th>
                        <th style="padding:8px 6px">Contact suivi dossier</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categoriesPpe as $cle => $libelle)
                        <tr style="border-top:1px solid #eeeae7">
                            <td style="padding:8px 6px">{{ $libelle }}</td>
                            <td style="padding:8px 6px"><input type="checkbox" name="ppe_reponses[{{ $cle }}][dirigeant_executif]" value="1" {{ ($ppeReponses[$cle]['dirigeant_executif'] ?? false) ? 'checked' : '' }}></td>
                            <td style="padding:8px 6px"><input type="checkbox" name="ppe_reponses[{{ $cle }}][contact_suivi_dossier]" value="1" {{ ($ppeReponses[$cle]['contact_suivi_dossier'] ?? false) ? 'checked' : '' }}></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            @foreach ([
                ['champ' => 'chiffre_affaires_n1', 'titre' => "Chiffre d'affaires N-1"],
                ['champ' => 'charges_n1', 'titre' => 'Charges N-1'],
                ['champ' => 'resultat_n1', 'titre' => 'Résultat N-1'],
                ['champ' => 'resultats_filiales', 'titre' => 'Résultats filiales'],
            ] as $bloc)
                <div class="wd-section-title">{{ $bloc['titre'] }}</div>
                <div x-data="wdRepeater(@js($lignesPostes($bloc['champ'])))">
                    <template x-for="(ligne, i) in items" :key="i">
                        <div class="wd-repeater-row wd-cols-5">
                            <div><input type="text" placeholder="Poste" :name="'{{ $bloc['champ'] }}['+i+'][poste]'" x-model="ligne.poste" style="width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px"></div>
                            <div><input type="number" step="0.01" placeholder="Montant" :name="'{{ $bloc['champ'] }}['+i+'][montant]'" x-model="ligne.montant" style="width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px"></div>
                            <div><input type="text" placeholder="Activité" :name="'{{ $bloc['champ'] }}['+i+'][activite]'" x-model="ligne.activite" style="width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px"></div>
                            <div><input type="text" placeholder="Hors France" :name="'{{ $bloc['champ'] }}['+i+'][hors_france]'" x-model="ligne.hors_france" style="width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px"></div>
                            <div><input type="text" placeholder="Remarques" :name="'{{ $bloc['champ'] }}['+i+'][remarques]'" x-model="ligne.remarques" style="width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px"></div>
                            <button type="button" class="wd-repeater-remove" @click="retirer(i)">Retirer</button>
                        </div>
                    </template>
                    <button type="button" class="wd-repeater-add" @click="ajouter()">+ Ajouter une ligne</button>
                </div>
            @endforeach

            <div class="wd-section-title">Évolutions prévisibles</div>
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field"><textarea rows="2" name="evolutions_previsibles">{{ $old('evolutions_previsibles') }}</textarea></div>
            </div>

            <div class="wd-section-title">Imposition</div>
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field wd-c2"><label>IS - Année dernière</label><input type="number" step="0.01" name="is_annee_derniere" value="{{ $old('is_annee_derniere') }}"></div>
                <div class="wd-cabinet-field wd-c2"><label>IS - Année jugée moyenne</label><input type="number" step="0.01" name="is_annee_moyenne" value="{{ $old('is_annee_moyenne') }}"></div>
                <div class="wd-cabinet-field wd-c2"><label>IS - Évolutions prévisibles</label><input type="text" name="is_evolutions_previsibles" value="{{ $old('is_evolutions_previsibles') }}"></div>
                <div class="wd-cabinet-field wd-c2"><label>Taxe pro - Année dernière</label><input type="number" step="0.01" name="taxe_professionnelle_annee_derniere" value="{{ $old('taxe_professionnelle_annee_derniere') }}"></div>
                <div class="wd-cabinet-field wd-c2"><label>Taxe pro - Année moyenne</label><input type="number" step="0.01" name="taxe_professionnelle_annee_moyenne" value="{{ $old('taxe_professionnelle_annee_moyenne') }}"></div>
                <div class="wd-cabinet-field wd-c2"><label>Taxe pro - Évolutions prévisibles</label><input type="text" name="taxe_professionnelle_evolutions_previsibles" value="{{ $old('taxe_professionnelle_evolutions_previsibles') }}"></div>
                <div class="wd-cabinet-field wd-c3"><label>Impôts fonciers (€)</label><input type="number" step="0.01" name="impots_fonciers" value="{{ $old('impots_fonciers') }}"></div>
                <div class="wd-cabinet-field wd-c3"><label>Autres impôts acquittés</label><input type="text" name="autres_impots_acquittes" value="{{ $old('autres_impots_acquittes') }}"></div>
                <div class="wd-cabinet-field"><label>Remarques</label><textarea rows="2" name="remarques">{{ $old('remarques') }}</textarea></div>
            </div>

            <div class="wd-section-title">Validation</div>
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field">
                    <label class="wd-checkbox-line" style="text-transform:none;font-size:12px;color:#242424">
                        <input type="checkbox" name="accepte_cgu" value="1" {{ $old('accepte_cgu', $kyc?->accepte_cgu) ? 'checked' : '' }}>
                        Informations vérifiées et confirmées avec le client
                    </label>
                </div>
            </div>

            <button type="submit" class="wd-cabinet-save">Enregistrer le KYC société</button>
        </form>
    </section>
</div>

<script>
function wdRepeater(items) {
    return {
        items: (items && items.length) ? items : [{}],
        ajouter() { this.items.push({}); },
        retirer(i) { this.items.splice(i, 1); if (! this.items.length) { this.items.push({}); } },
    };
}
</script>
</x-tenant-app-layout>
