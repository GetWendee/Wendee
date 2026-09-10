<x-tenant-app-layout>
<div class="wd-wrap-rdv">
<style>
.wd-wrap-rdv{max-width:900px;margin:0 auto;padding:28px 24px 60px;}
.wd-rdv-title{font-size:26px;font-weight:800;letter-spacing:-.03em;color:#151515;margin-bottom:0;}
.wd-rdv-section-title{font-size:11px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#9a928d;margin:28px 0 12px;}
.wd-rdv-list{background:#fff;border-radius:12px;border:1px solid #ded9d4;overflow:hidden;}
.wd-rdv-row{padding:14px 18px;border-bottom:1px solid #eeeae7;}
.wd-rdv-row:last-child{border-bottom:0;}
.wd-rdv-date{font-size:13px;font-weight:700;color:#151515;}
.wd-rdv-sujet{font-size:12.5px;color:#66605c;margin-top:2px;}
.wd-rdv-empty{padding:20px 18px;font-size:13px;color:#817b76;}
.wd-rdv-header{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:24px;}
.wd-rdv-add-btn{width:38px;height:38px;border-radius:50%;background:#f40087;color:#fff;border:none;font-size:19px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;flex:0 0 auto;transition:background .15s ease;}
.wd-rdv-add-btn:hover{background:#c40070;}
.wd-rdv-overlay{position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(21,21,21,.45);color-scheme:light;}
@media(max-width:600px){.wd-wrap-rdv{padding:18px 14px 40px;}}
</style>

@php
$sujetLabels = [
    'point_etape' => "Point d'étape",
    'bilan_patrimonial' => 'Bilan patrimonial',
    'signature_document' => 'Signature document',
    'suivi_portefeuille' => 'Suivi portefeuille',
    'autre' => 'Autre',
];
@endphp

<div class="wd-rdv-header">
<h1 class="wd-rdv-title">Mes rendez-vous</h1>
<button type="button" class="wd-rdv-add-btn" x-data x-on:click="$dispatch('ouvrir-mes-rdv')">+</button>
</div>

<div class="wd-rdv-section-title">À venir</div>
<div class="wd-rdv-list">
@forelse($rendezVousAVenir as $rdv)
<div class="wd-rdv-row">
<div class="wd-rdv-date">{{ $rdv->starts_at->translatedFormat('d F Y à H:i') }}</div>
<div class="wd-rdv-sujet">{{ $sujetLabels[$rdv->sujet] ?? $rdv->titre }}</div>
</div>
@empty
<div class="wd-rdv-empty">Aucun rendez-vous à venir.</div>
@endforelse
</div>

<div class="wd-rdv-section-title">Passés</div>
<div class="wd-rdv-list">
@forelse($rendezVousPasses as $rdv)
<div class="wd-rdv-row">
<div class="wd-rdv-date">{{ $rdv->starts_at->translatedFormat('d F Y à H:i') }}</div>
<div class="wd-rdv-sujet">{{ $sujetLabels[$rdv->sujet] ?? $rdv->titre }}</div>
</div>
@empty
<div class="wd-rdv-empty">Aucun rendez-vous passé.</div>
@endforelse
</div>

<div x-data="{ visible: @js(session('status') === 'demande-rdv-envoyee'), envoye: @js(session('status') === 'demande-rdv-envoyee') }" x-on:ouvrir-mes-rdv.window="visible = true">
<template x-teleport="body">
    <div class="wd-rdv-overlay" x-show="visible" x-cloak>
        <div style="background:#fff;border-radius:16px;padding:28px;max-width:460px;width:92%;max-height:80vh;overflow-y:auto;" x-on:click.outside="visible = false">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
                <h2 style="font-size:17px;font-weight:800;color:#151515;">Demander un rendez-vous</h2>
                <button type="button" x-on:click="visible = false" style="background:none;border:none;font-size:20px;cursor:pointer;color:#817b76;">&times;</button>
            </div>

            <template x-if="!envoye">
                <form method="POST" action="{{ route('tenant.clients.rendez-vous.demander', $client) }}">
                    @csrf
                    <input type="hidden" name="urgent" value="0">
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:#151515;margin-bottom:14px;">
                        <input type="checkbox" name="urgent" value="1" style="width:16px;height:16px;">
                        C'est urgent
                    </label>

                    <label style="display:block;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#9a928d;margin-bottom:8px;">Sujet</label>
                    <textarea name="sujet" required rows="4" style="width:100%;border:1px solid #ded9d4;border-radius:8px;padding:10px 12px;font-size:13px;color:#151515;margin-bottom:16px;font-family:inherit;" placeholder="Expliquez brièvement l'objet de votre demande"></textarea>

                    <button type="submit" style="width:100%;background:#242424;color:#fff;border:none;border-radius:8px;padding:12px;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;cursor:pointer;">
                        Demander un rendez-vous
                    </button>
                </form>
            </template>

            <template x-if="envoye">
                <p style="font-size:13.5px;color:#151515;">
                    Votre demande a bien été envoyée. Votre conseiller reviendra vers vous rapidement.
                </p>
            </template>
        </div>
    </div>
</template>
</div>

</div>
</x-tenant-app-layout>
