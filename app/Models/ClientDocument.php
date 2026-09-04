<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientDocument extends Model
{
    protected $fillable = [
        'client_id',
        'type',
        'chemin',
        'nom_original',
        'taille',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
