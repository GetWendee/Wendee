<x-tenant-app-layout>
@include('tenant.clients.partials.header-tabs', ['active' => 'mission'])
<style>
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
</style>
<section class="wd-section">
    <div class="wd-mission-filtres">
        @foreach(['audit' => 'Audit', 'etude' => 'Étude', 'simulation' => 'Simulation'] as $valeur => $label)
            <label class="wd-mission-filtre">
                <input type="checkbox" class="wd-mission-filtre-checkbox" value="{{ $valeur }}" data-mission-filtre {{ $valeur === 'audit' ? 'checked' : '' }}>
                <span class="wd-mission-filtre-box">
                    <svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                </span>
                <span>{{ $label }}</span>
            </label>
        @endforeach
    </div>
    <div class="wd-mission-grille" data-mission-grille>
        @foreach(config('prestations') as $prestation)
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
        @endforeach
    </div>
</section>
<script>
(function () {
    var grille = document.querySelector('[data-mission-grille]');
    var filtres = document.querySelectorAll('[data-mission-filtre]');
    if (! grille) { return; }
    function appliquer() {
        var actifs = Array.prototype.filter.call(filtres, function (c) { return c.checked; })
            .map(function (c) { return c.value; });
        grille.querySelectorAll('[data-famille]').forEach(function (carte) {
            carte.style.display = actifs.indexOf(carte.getAttribute('data-famille')) !== -1 ? '' : 'none';
        });
    }
    filtres.forEach(function (c) { c.addEventListener('change', appliquer); });
    appliquer();
})();
</script>
</x-tenant-app-layout>
