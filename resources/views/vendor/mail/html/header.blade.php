@props(['url'])
@php
$cabinetProfile = null;
if (function_exists('tenant') && tenant()) {
    try {
        $cabinetProfile = \App\Models\CabinetProfile::query()->first();
    } catch (\Throwable $e) {
        $cabinetProfile = null;
    }
}
$cabinetName = $cabinetProfile?->nom_commercial ?: null;
$cabinetLogo = $cabinetProfile && $cabinetProfile->logo
    ? asset('storage/' . $cabinetProfile->logo)
    : null;
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
@if ($cabinetLogo)
<img src="{{ $cabinetLogo }}" alt="{{ $cabinetName ?? 'Logo' }}" style="max-height: 48px; max-width: 220px;">
@elseif ($cabinetName)
<span style="font-size: 22px; font-weight: 800; letter-spacing: -0.03em; color: #1b1716;">
{{ $cabinetName }}
</span>
@else
<span style="font-size: 22px; font-weight: 800; letter-spacing: -0.03em;">
<span style="color: #f40087;">W</span><span style="color: #1b1716;">endee</span>
</span>
@endif
</a>
</td>
</tr>
