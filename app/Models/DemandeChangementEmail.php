<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandeChangementEmail extends Model
{
    use HasFactory;

    protected $table = 'demandes_changement_email';

    protected $fillable = ['user_id', 'ancien_email', 'nouvel_email', 'statut', 'expire_le'];

    protected function casts(): array
    {
        return [
            'expire_le' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function estEnAttente(): bool
    {
        return $this->statut === 'en_attente' && $this->expire_le->isFuture();
    }
}
