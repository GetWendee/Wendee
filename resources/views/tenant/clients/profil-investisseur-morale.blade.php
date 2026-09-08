@php
    $piMorale = $client->profilInvestisseurMorale;
    $old = fn($field, $default = null) => old($field, $piMorale?->{$field} ?? $default);
    $objectifs = old('objectifs', $piMorale?->objectifs ?? []);
    $indicateursEnv = old('esg_indicateurs_environnementaux', $piMorale?->esg_indicateurs_environnementaux ?? []);
    $indicateursSociaux = old('esg_indicateurs_sociaux', $piMorale?->esg_indicateurs_sociaux ?? []);
@endphp
<x-tenant-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Profil investisseur société - {{ $client->nomAffichage() }}
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
.wd-cabinet-field input[type=text],.wd-cabinet-field input[type=number],.wd-cabinet-field select,.wd-cabinet-field textarea{width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px;color:#242424;background:#fff;font-family:inherit}
.wd-cabinet-field input:focus,.wd-cabinet-field select:focus,.wd-cabinet-field textarea:focus{outline:none;border-color:#f40087}
.wd-section-title{margin:26px 0 4px;font-size:11px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#9a928d}
.wd-repeater-row{display:grid;grid-template-columns:1fr 160px auto;gap:10px;align-items:end;padding:10px 0;border-bottom:1px solid #eeeae7}
.wd-repeater-row.wd-cols-1{grid-template-columns:1fr auto}
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
        <form method="POST" action="{{ route('tenant.clients.profil-investisseur-morale.update', $client) }}">
            @csrf
            @method('PUT')

            <div class="wd-section-title">Profil de risque</div>
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field wd-c3">
                    <label>Profil de risque (1 à 7)</label>
                    <select name="profil_risque">
                        <option value="">-</option>
                        @for ($i = 1; $i <= 7; $i++)
                            <option value="{{ $i }}" {{ (string) $old('profil_risque') === (string) $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="wd-section-title">Objectifs</div>
            <div x-data="wdRepeater(@js($objectifs))">
                <template x-for="(ligne, i) in items" :key="i">
                    <div class="wd-repeater-row">
                        <div><label style="display:block;color:#9a928d;font-size:8px;font-weight:800;text-transform:uppercase;margin-bottom:6px">Objectif</label>
                            <input type="text" :name="'objectifs['+i+'][libelle]'" x-model="ligne.libelle" style="width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px">
                        </div>
                        <div><label style="display:block;color:#9a928d;font-size:8px;font-weight:800;text-transform:uppercase;margin-bottom:6px">Horizon (années)</label>
                            <input type="number" min="0" :name="'objectifs['+i+'][horizon_annees]'" x-model="ligne.horizon_annees" style="width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px">
                        </div>
                        <button type="button" class="wd-repeater-remove" @click="retirer(i)">Retirer</button>
                    </div>
                </template>
                <button type="button" class="wd-repeater-add" @click="ajouter()">+ Ajouter un objectif</button>
            </div>

            <div class="wd-section-title">Critères extra-financiers (ESG)</div>
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field wd-c3">
                    <label class="wd-checkbox-line" style="text-transform:none;font-size:12px;color:#242424">
                        <input type="checkbox" name="esg_interesse" value="1" {{ $old('esg_interesse') ? 'checked' : '' }}>
                        Intéressé par des placements ESG
                    </label>
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label class="wd-checkbox-line" style="text-transform:none;font-size:12px;color:#242424">
                        <input type="checkbox" name="esg_accepte_performance_moindre" value="1" {{ $old('esg_accepte_performance_moindre') ? 'checked' : '' }}>
                        Accepte une performance potentiellement moindre
                    </label>
                </div>
                <div class="wd-cabinet-field wd-c2">
                    <label class="wd-checkbox-line" style="text-transform:none;font-size:12px;color:#242424">
                        <input type="checkbox" name="esg_taxonomie" value="1" {{ $old('esg_taxonomie') ? 'checked' : '' }}>
                        Taxonomie européenne
                    </label>
                </div>
                <div class="wd-cabinet-field wd-c2">
                    <label class="wd-checkbox-line" style="text-transform:none;font-size:12px;color:#242424">
                        <input type="checkbox" name="esg_sfdr" value="1" {{ $old('esg_sfdr') ? 'checked' : '' }}>
                        SFDR
                    </label>
                </div>
                <div class="wd-cabinet-field wd-c2">
                    <label class="wd-checkbox-line" style="text-transform:none;font-size:12px;color:#242424">
                        <input type="checkbox" name="esg_pai" value="1" {{ $old('esg_pai') ? 'checked' : '' }}>
                        PAI (principales incidences négatives)
                    </label>
                </div>
                <div class="wd-cabinet-field wd-c3"><label>Objectif ESG (% du placement)</label><input type="number" step="0.01" min="0" max="100" name="esg_objectif_pourcentage" value="{{ $old('esg_objectif_pourcentage') }}"></div>
                <div class="wd-cabinet-field wd-c3"><label>Objectif ESG (précisions libres)</label><input type="text" name="esg_objectif_libre" value="{{ $old('esg_objectif_libre') }}"></div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Profil ESG de l'investissement (1 à 7)</label>
                    <select name="esg_profil_investissement">
                        <option value="">-</option>
                        @for ($i = 1; $i <= 7; $i++)
                            <option value="{{ $i }}" {{ (string) $old('esg_profil_investissement') === (string) $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Profil ESG du patrimoine global (1 à 7)</label>
                    <select name="esg_profil_patrimoine_global">
                        <option value="">-</option>
                        @for ($i = 1; $i <= 7; $i++)
                            <option value="{{ $i }}" {{ (string) $old('esg_profil_patrimoine_global') === (string) $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="wd-cabinet-field wd-c3"><label>Patrimoine global ESG (% du patrimoine)</label><input type="number" step="0.01" min="0" max="100" name="esg_patrimoine_global_pourcentage" value="{{ $old('esg_patrimoine_global_pourcentage') }}"></div>
                <div class="wd-cabinet-field wd-c3"><label>Patrimoine global ESG (précisions libres)</label><input type="text" name="esg_patrimoine_global_libre" value="{{ $old('esg_patrimoine_global_libre') }}"></div>
                <div class="wd-cabinet-field"><label>Objectifs / besoins / horizon (synthèse libre)</label><textarea rows="3" name="esg_objectifs_besoins_horizon">{{ $old('esg_objectifs_besoins_horizon') }}</textarea></div>
            </div>

            <div class="wd-section-title">Indicateurs environnementaux suivis</div>
            <div x-data="wdRepeaterTexte(@js($indicateursEnv))">
                <template x-for="(ligne, i) in items" :key="i">
                    <div class="wd-repeater-row wd-cols-1">
                        <div><input type="text" placeholder="Indicateur environnemental" :name="'esg_indicateurs_environnementaux['+i+']'" x-model="items[i]" style="width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px"></div>
                        <button type="button" class="wd-repeater-remove" @click="retirer(i)">Retirer</button>
                    </div>
                </template>
                <button type="button" class="wd-repeater-add" @click="ajouter()">+ Ajouter un indicateur</button>
            </div>

            <div class="wd-section-title">Indicateurs sociaux suivis</div>
            <div x-data="wdRepeaterTexte(@js($indicateursSociaux))">
                <template x-for="(ligne, i) in items" :key="i">
                    <div class="wd-repeater-row wd-cols-1">
                        <div><input type="text" placeholder="Indicateur social" :name="'esg_indicateurs_sociaux['+i+']'" x-model="items[i]" style="width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px"></div>
                        <button type="button" class="wd-repeater-remove" @click="retirer(i)">Retirer</button>
                    </div>
                </template>
                <button type="button" class="wd-repeater-add" @click="ajouter()">+ Ajouter un indicateur</button>
            </div>

            <div class="wd-section-title">Validation</div>
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field">
                    <label class="wd-checkbox-line" style="text-transform:none;font-size:12px;color:#242424">
                        <input type="checkbox" name="accepte_cgu" value="1" {{ $old('accepte_cgu', $piMorale?->accepte_cgu) ? 'checked' : '' }}>
                        Informations vérifiées et confirmées avec le client
                    </label>
                </div>
            </div>

            <button type="submit" class="wd-cabinet-save">Enregistrer le profil investisseur société</button>
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
function wdRepeaterTexte(items) {
    return {
        items: (items && items.length) ? items : [''],
        ajouter() { this.items.push(''); },
        retirer(i) { this.items.splice(i, 1); if (! this.items.length) { this.items.push(''); } },
    };
}
</script>
</x-tenant-app-layout>
