<?php

namespace App\Services;

use App\Mail\PromptIaCodeMail;
use App\Models\PromptIa;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

/**
 * Prompts système des services IA (App\Services\AI\*), pilotés depuis la
 * page centrale "Configuration IA" (onglet réservé à l'équipe Wendee)
 * plutôt que codés en dur.
 *
 * resolve() est appelé par chaque service IA pour récupérer le prompt
 * actif. proposerModification() / confirmer() gèrent le flux d'édition :
 * toute sauvegarde passe par un code de confirmation envoyé par email à
 * l'auteur de la modification avant d'être réellement appliquée — le champ
 * "contenu" n'est jamais écrit directement depuis le formulaire.
 */
class PromptIaService
{
    private const CARACTERES = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    /**
     * Contenu actif d'un prompt, avec repli sur $defaut si la ligne est
     * absente ou vide en base (aucun risque de casser un moteur IA si la
     * migration n'a pas encore tourné ou si la ligne a été supprimée).
     *
     * $remplacements permet d'injecter des valeurs dynamiques dans le
     * texte édité (ex : ['{{total_honoraires}}' => '1 200,00 €']),
     * remplacement fait par strtr() après lecture, jamais dans le prompt
     * stocké lui-même.
     */
    public function resolve(string $cle, string $defaut, array $remplacements = []): string
    {
        $contenu = PromptIa::where('cle', $cle)->value('contenu');

        if (empty($contenu)) {
            $contenu = $defaut;
        }

        return $remplacements ? strtr($contenu, $remplacements) : $contenu;
    }

    public function proposerModification(PromptIa $prompt, string $nouveauContenu, User $auteur): void
    {
        if (empty($auteur->email)) {
            throw new RuntimeException(
                "Aucune adresse email sur ce compte, impossible d'envoyer le code de confirmation."
            );
        }

        $code = $this->genererCode();

        $prompt->update([
            'pending_contenu' => $nouveauContenu,
            'code_verification' => $code,
            'code_envoye_le' => now(),
            'modifie_par_user_id' => $auteur->id,
        ]);

        Mail::to($auteur->email)->send(new PromptIaCodeMail($prompt, $auteur, $code));
    }

    public function confirmer(PromptIa $prompt, string $code): bool
    {
        if (
            $prompt->code_verification === null
            || $prompt->pending_contenu === null
            || strtoupper(trim($code)) !== $prompt->code_verification
        ) {
            return false;
        }

        $prompt->update([
            'contenu' => $prompt->pending_contenu,
            'pending_contenu' => null,
            'code_verification' => null,
            'code_envoye_le' => null,
        ]);

        return true;
    }

    public function annulerModification(PromptIa $prompt): void
    {
        $prompt->update([
            'pending_contenu' => null,
            'code_verification' => null,
            'code_envoye_le' => null,
        ]);
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
