<?php

namespace App\Services;

use App\Mail\CodeVerificationMail;
use App\Mail\ModificationEffectueeMail;
use App\Models\Client;
use App\Models\VerificationClient;
use Illuminate\Support\Facades\Mail;

class VerificationCodeService
{
    private const CARACTERES = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public function enregistrerModification(Client $client, string $module): void
    {
        if (empty($client->email)) {
            return;
        }

        $verification = VerificationClient::where('client_id', $client->id)
            ->where('module', $module)
            ->first();

        if (! $verification) {
            $code = $this->genererCode();

            VerificationClient::create([
                'client_id' => $client->id,
                'module' => $module,
                'code' => $code,
                'code_envoye_le' => now(),
            ]);

            Mail::to($client->email)->send(new CodeVerificationMail($client, $module, $code));

            return;
        }

        Mail::to($client->email)->send(new ModificationEffectueeMail($client, $module));
    }

    public function verifierCode(Client $client, string $module, string $code): bool
    {
        $verification = VerificationClient::where('client_id', $client->id)
            ->where('module', $module)
            ->first();

        if (! $verification || $verification->code === null || strtoupper($code) !== $verification->code) {
            return false;
        }

        $verification->update(['verifie_le' => now()]);

        return true;
    }

    private function genererCode(): string
    {
        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $code .= self::CARACTERES[random_int(0, strlen(self::CARACTERES) - 1)];
        }

        return $code;
    }
}
