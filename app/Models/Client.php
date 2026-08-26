<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'civilite', 'prenom', 'nom', 'date_naissance',
    'telephone_mobile', 'telephone_domicile', 'email',
    'adresse', 'code_postal', 'ville', 'pays', 'conseiller_id',
])]
class Client extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
        ];
    }

    public function conseiller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conseiller_id');
    }
}
