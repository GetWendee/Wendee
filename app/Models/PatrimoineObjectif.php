<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatrimoineObjectif extends Model
{
    protected $table = 'patrimoine_objectifs';

    protected $fillable = [
        'client_id',
        'objectif',
        'horizon',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
