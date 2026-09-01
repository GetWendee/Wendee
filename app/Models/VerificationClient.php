<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationClient extends Model
{
    protected $table = 'verifications_client';

    protected $fillable = [
        'client_id',
        'module',
        'code',
        'code_envoye_le',
        'verifie_le',
    ];

    protected function casts(): array
    {
        return [
            'code_envoye_le' => 'datetime',
            'verifie_le' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
