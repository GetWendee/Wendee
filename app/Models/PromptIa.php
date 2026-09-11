<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromptIa extends Model
{
    protected $table = 'prompts_ia';

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
