<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromptIa extends Model
{
    protected $table = 'prompts_ia';

    /**
     * prompts_ia est une table centrale (unique, partagée par tous les
     * cabinets) : la page "Configuration IA" est un écran central, et les
     * 12 moteurs IA doivent tous lire la même ligne quel que soit le
     * cabinet depuis lequel ils sont appelés.
     *
     * Sans cette connexion forcée, une requête faite depuis un contexte
     * tenant (ex : clic sur "Suggérer" sur une page tenant) utilisait la
     * connexion par défaut basculée par stancl/tenancy sur la base du
     * cabinet — où prompts_ia n'existe pas ("Base table or view not
     * found"). On force donc explicitement la connexion centrale,
     * identique à celle utilisée par stancl/tenancy lui-même.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setConnection(config('tenancy.database.central_connection'));
    }

    protected $fillable = [
        'cle', 'titre', 'contenu',
        'pending_contenu', 'code_verification', 'code_envoye_le',
        'modifie_par_user_id',
    ];

    protected function casts(): array
    {
        return [
            'code_envoye_le' => 'datetime',
        ];
    }

    public function modifiePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modifie_par_user_id');
    }

    public function enAttenteDeConfirmation(): bool
    {
        return ! empty($this->pending_contenu) && ! empty($this->code_verification);
    }
}
