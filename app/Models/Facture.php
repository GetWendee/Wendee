<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facture extends Model
{
    protected $fillable = [
        'apporteur_id',
        'client_id',
        'fichier_path',
        'nom_original',
        'mime_type',
        'taille',
        'statut',
    ];

    public function apporteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'apporteur_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
