<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Tache extends Model
{
    protected $table = 'taches';
    protected $fillable = [
        'titre',
        'page_module',
        'description',
        'fait',
    ];
    protected $casts = [
        'fait' => 'boolean',
    ];
    public function piecesJointes(): HasMany
    {
        return $this->hasMany(TachePieceJointe::class);
    }
}
