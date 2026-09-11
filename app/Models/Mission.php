<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mission extends Model
{
    protected $fillable = [
        'client_id',
        'suggestion_id',
        'prestation_id',
        'prestation_snapshot',
        'categorie',
        'type_document',
        'status',
        'input_version',
        'prompt_version',
        'model',
        'input_data',
        'result_json',
        'raw_response',
        'edited_json',
        'valide_le',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'started_at',
        'completed_at',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'prestation_snapshot' => 'array',
            'input_data' => 'array',
            'result_json' => 'array',
            'edited_json' => 'array',
            'valide_le' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function suggestion(): BelongsTo
    {
        return $this->belongsTo(ClientAnalysis::class, 'suggestion_id');
    }

    /**
     * Contenu à afficher/éditer : la version relue par le conseiller si elle
     * existe déjà, sinon la sortie brute d'IA 2A.
     */
    public function contenuAffiche(): ?array
    {
        return $this->edited_json ?? $this->result_json;
    }
}
