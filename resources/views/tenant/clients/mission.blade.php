<x-tenant-app-layout>
@include('tenant.clients.partials.header-tabs', ['active' => 'mission'])
@php $viewRole = Auth::user()?->effectiveRole(); @endphp
<style>

.wd-rdv-overlay{
    position:fixed;
    inset:0;
    z-index:9999;
    display:flex;
    align-items:center;
    justify-content:center;
    background:rgba(21,21,21,.45);
    color-scheme:light;
}
.wd-mission-section-title{font-weight:800;font-size:16px;color:var(--ink);margin:28px 0 4px;}
.wd-mission-section-title:first-of-type{margin-top:6px;}
.wd-mission-filtres{display:flex;gap:16px;margin:22px 0 20px;flex-wrap:wrap;justify-content:center;}
.wd-mission-filtre{display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--muted);}
.wd-mission-filtre input{display:none;}
.wd-mission-filtre-box{width:16px;height:16px;flex:0 0 16px;display:grid;place-items:center;border:1px solid #ded9d4;border-radius:4px;background:#fff;}
.wd-mission-filtre-box svg{width:10px;height:10px;display:none;fill:none;stroke:#fff;stroke-width:3;stroke-linecap:round;stroke-linejoin:round;}
.wd-mission-filtre:has(input:checked){color:var(--pink);font-weight:700;}
.wd-mission-filtre:has(input:checked) .wd-mission-filtre-box{border-color:var(--pink);background:var(--pink);}
.wd-mission-filtre:has(input:checked) .wd-mission-filtre-box svg{display:block;}
.wd-mission-grille{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;}
@media (max-width:1200px){.wd-mission-grille{grid-template-columns:repeat(2,1fr);}}
@media (max-width:640px){.wd-mission-grille{grid-template-columns:1fr;}}
.wd-mission-carte{background:#fff;border:1px solid var(--line);border-radius:14px;padding:22px;text-decoration:none;color:inherit;display:block;transition:transform .15s ease,box-shadow .15s ease;position:relative;}
.wd-mission-carte:hover{transform:translateY(-3px);box-shadow:0 10px 24px rgba(23,21,20,.08);}
.wd-mission-carte.wd-mission-indisponible{cursor:not-allowed;opacity:.5;}
.wd-mission-carte.wd-mission-indisponible:hover{transform:none;box-shadow:none;}
.wd-mission-icone{margin-bottom:12px;}.wd-mission-icone svg{width:26px;height:26px;stroke:var(--pink);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}
.wd-mission-titre{font-weight:800;font-size:15px;color:var(--ink);margin-bottom:4px;}
.wd-mission-sous-titre{color:var(--muted);font-size:12px;}
.wd-mission-badge{margin-top:12px;display:inline-block;font-size:10px;background:var(--soft);color:var(--muted);padding:4px 10px;border-radius:20px;}

.wd-interet-btn-oui{flex:1;background:#242424;color:#fff;border:none;border-radius:8px;padding:12px;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;cursor:pointer;transition:background .15s ease;}
.wd-interet-btn-oui:hover{background:#f40087;}
.wd-interet-btn-oui:disabled{opacity:.4;cursor:not-allowed;}
.wd-interet-btn-non{flex:1;background:none;border:1px solid #ded9d4;border-radius:8px;padding:12px;font-size:12px;font-weight:700;cursor:pointer;transition:border-color .15s ease,color .15s ease;}
.wd-interet-btn-non:hover{border-color:#151515;color:#151515;}
.wd-interet-delai{border:1px solid #ded9d4;border-radius:20px;padding:8px 14px;font-size:12px;font-weight:700;background:#fff;cursor:pointer;transition:all .15s ease;}
.wd-interet-delai:hover{border-color:#242424;}
.wd-interet-delai-actif{background:#242424;color:#fff;border-color:#242424;}
</style>

<h3 class="wd-mission-section-title">Nos services</h3>
<section class="wd-section">
    <div class="wd-mission-filtres">
        @foreach(['audit' => 'Audit', 'etude' => 'Étude', 'simulation' => 'Simulation'] as $valeur => $label)
            <label class="wd-mission-filtre">
                <input type="checkbox" class="wd-mission-filtre-checkbox" value="{{ $valeur }}" data-mission-filtre="services" {{ $valeur === 'audit' ? 'checked' : '' }}>
                <span class="wd-mission-filtre-box">
                    <svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                </span>
                <span>{{ $label }}</span>
            </label>
        @endforeach
    </div>
    <div class="wd-mission-grille" data-mission-grille="services">
        @foreach(config('prestations') as $prestation)
            @if($viewRole === 'client')
                <div
                    x-data="{}"
                    class="wd-mission-carte"
                    style="cursor:pointer;"
                    role="button"
                    tabindex="0"
                    data-famille="{{ $prestation['famille'] }}"
                    x-on:click="$dispatch('ouvrir-interet', { titre: @js($prestation['titre']), icone: @js($prestation['icone']) })"
                    x-on:keydown.enter="$dispatch('ouvrir-interet', { titre: @js($prestation['titre']), icone: @js($prestation['icone']) })"
                >
                    <div class="wd-mission-icone"><svg viewBox="0 0 24 24">{!! $prestation['icone'] !!}</svg></div>
                    <div class="wd-mission-titre">{{ $prestation['titre'] }}</div>
                    <div class="wd-mission-sous-titre">{{ $prestation['sous_titre'] }}</div>
                </div>
            @else
                @php
                    $disponible = (bool) $prestation['route'];
                    $balise = $disponible ? 'a' : 'div';
                @endphp
                <{{ $balise }}
                    class="wd-mission-carte{{ $disponible ? '' : ' wd-mission-indisponible' }}"
                    data-famille="{{ $prestation['famille'] }}"
                    @if($disponible) href="{{ route($prestation['route'], $client) }}" @endif
                >
                    <div class="wd-mission-icone"><svg viewBox="0 0 24 24">{!! $prestation['icone'] !!}</svg></div>
                    <div class="wd-mission-titre">{{ $prestation['titre'] }}</div>
                    <div class="wd-mission-sous-titre">{{ $prestation['sous_titre'] }}</div>
                    @if(! $disponible)
                        <div class="wd-mission-badge">Indisponible pour le moment</div>
                    @endif
                </{{ $balise }}>
            @endif
        @endforeach
    </div>
</section>

<h3 class="wd-mission-section-title">Nos solutions</h3>
<section class="wd-section">
    <div class="wd-mission-filtres">
        @foreach(['fam_assurance' => 'Assurance', 'fam_banque' => 'Banque', 'fam_immobilier' => 'Immobilier', 'famille_fin' => 'Finance'] as $valeur => $label)
            <label class="wd-mission-filtre">
                <input type="checkbox" class="wd-mission-filtre-checkbox" value="{{ $valeur }}" data-mission-filtre="solutions" checked>
                <span class="wd-mission-filtre-box">
                    <svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                </span>
                <span>{{ $label }}</span>
            </label>
        @endforeach
    </div>
    <div class="wd-mission-grille" data-mission-grille="solutions">
        @foreach(config('solutions') as $solution)
            @if($viewRole === 'client')
                <div
                    x-data="{}"
                    class="wd-mission-carte"
                    style="cursor:pointer;"
                    role="button"
                    tabindex="0"
                    data-famille="{{ $solution['famille'] }}"
                    x-on:click="$dispatch('ouvrir-interet', { titre: @js($solution['titre']), icone: @js($solution['icone']) })"
                    x-on:keydown.enter="$dispatch('ouvrir-interet', { titre: @js($solution['titre']), icone: @js($solution['icone']) })"
                >
                    <div class="wd-mission-icone"><svg viewBox="0 0 24 24">{!! $solution['icone'] !!}</svg></div>
                    <div class="wd-mission-titre">{{ $solution['titre'] }}</div>
                    <div class="wd-mission-sous-titre">{{ $solution['sous_titre'] }}</div>
                </div>
            @else
                @php
                    $disponible = (bool) $solution['route'];
                    $balise = $disponible ? 'a' : 'div';
                @endphp
                <{{ $balise }}
                    class="wd-mission-carte{{ $disponible ? '' : ' wd-mission-indisponible' }}"
                    data-famille="{{ $solution['famille'] }}"
                    @if($disponible) href="{{ route($solution['route'], $client) }}" @endif
                >
                    <div class="wd-mission-icone"><svg viewBox="0 0 24 24">{!! $solution['icone'] !!}</svg></div>
                    <div class="wd-mission-titre">{{ $solution['titre'] }}</div>
                    <div class="wd-mission-sous-titre">{{ $solution['sous_titre'] }}</div>
                    @if(! $disponible)
                        <div class="wd-mission-badge">Indisponible pour le moment</div>
                    @endif
                </{{ $balise }}>
            @endif
        @endforeach
    </div>
</section>

<script>
(function () {
    document.querySelectorAll('[data-mission-grille]').forEach(function (grille) {
        var groupe = grille.getAttribute('data-mission-grille');
        var filtres = document.querySelectorAll('[data-mission-filtre="' + groupe + '"]');
        function appliquer() {
            var actifs = Array.prototype.filter.call(filtres, function (c) { return c.checked; })
                .map(function (c) { return c.value; });
            grille.querySelectorAll('[data-famille]').forEach(function (carte) {
                carte.style.display = actifs.indexOf(carte.getAttribute('data-famille')) !== -1 ? '' : 'none';
            });
        }
        filtres.forEach(function (c) { c.addEventListener('change', appliquer); });
        appliquer();
    });
})();
</script>
@if($viewRole === 'client')
<div x-data="{ modalOpen: false, titreChoisi: '', iconeChoisie: '', etape: 1, delai: '' }" x-on:ouvrir-interet.window="titreChoisi = $event.detail.titre; iconeChoisie = $event.detail.icone; etape = 1; delai = ''; modalOpen = true">
<template x-teleport="body">
    <div class="wd-rdv-overlay" x-show="modalOpen" x-cloak>
        <div style="background:#fff;border-radius:16px;padding:28px;max-width:420px;width:92%;" x-on:click.outside="modalOpen = false">

            <template x-if="etape === 1">
                <div>
                    <div class="wd-mission-icone" style="margin:0 auto 16px;" x-html="iconeChoisie ? '&lt;svg viewBox=&quot;0 0 24 24&quot;&gt;' + iconeChoisie + '&lt;/svg&gt;' : ''"></div>
                    <p style="font-size:14px;color:#151515;margin-bottom:20px;text-align:center;">
                        Souhaitez-vous discuter <span x-text="/^[aeiouyhàâäéèêëïîôöùûü]/i.test(titreChoisi) ? 'd\'' : 'de '"></span><strong x-text="titreChoisi"></strong> avec {{ $client->conseiller?->name ?? 'votre conseiller' }} ?
                    </p>
                    <div style="display:flex;gap:10px;">
                        <button type="button" class="wd-interet-btn-oui" x-on:click="etape = 2">Oui</button>
                        <button type="button" class="wd-interet-btn-non" x-on:click="modalOpen = false">Non</button>
                    </div>
                </div>
            </template>

            <template x-if="etape === 2">
                <form method="POST" action="{{ route('tenant.clients.mission.interet', $client) }}">
                    @csrf
                    <input type="hidden" name="titre" x-bind:value="titreChoisi">
                    <input type="hidden" name="delai" x-bind:value="delai">
                    <p style="font-size:14px;color:#151515;margin-bottom:14px;">Quel est le délai souhaité ?</p>
                    <div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;">
                        <button type="button" class="wd-interet-delai" x-bind:class="delai === 'urgent' ? 'wd-interet-delai-actif' : ''" x-on:click="delai = 'urgent'">Urgent</button>
                        <button type="button" class="wd-interet-delai" x-bind:class="delai === 'bientot' ? 'wd-interet-delai-actif' : ''" x-on:click="delai = 'bientot'">Bientôt</button>
                        <button type="button" class="wd-interet-delai" x-bind:class="delai === 'sans_urgence' ? 'wd-interet-delai-actif' : ''" x-on:click="delai = 'sans_urgence'">Sans urgence</button>
                    </div>
                    <textarea name="notes" placeholder="Un détail à ajouter ? (facultatif)" style="width:100%;min-height:70px;border:1px solid #ded9d4;border-radius:8px;padding:10px;font-size:13px;margin-bottom:16px;resize:vertical;box-sizing:border-box;"></textarea>
                    <div style="display:flex;gap:10px;">
                        <button type="submit" class="wd-interet-btn-oui" x-bind:disabled="! delai">Envoyer</button>
                        <button type="button" class="wd-interet-btn-non" x-on:click="etape = 1">Retour</button>
                    </div>
                </form>
            </template>

        </div>
    </div>
</template>
</div>
@endif
</x-tenant-app-layout>
