<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RendezVous extends Model
{
    use HasFactory;

    protected $table = 'rendez_vous';

    protected $fillable = [
        'client_id',
        'user_id',
        'titre',
        'format',
        'sujet',
        'notes',
        'starts_at',
        'ends_at',
        'statut',
        'calendar_provider',
        'external_event_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function conseiller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
