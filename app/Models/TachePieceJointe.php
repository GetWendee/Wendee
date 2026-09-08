<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class TachePieceJointe extends Model
{
    protected $table = 'tache_pieces_jointes';
    protected $fillable = [
        'tache_id',
        'fichier_path',
        'nom_original',
        'mime_type',
        'taille',
    ];
    protected $casts = [
        'taille' => 'integer',
    ];
    public function tache(): BelongsTo
    {
        return $this->belongsTo(Tache::class);
    }
}
