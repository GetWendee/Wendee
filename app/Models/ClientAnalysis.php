<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientAnalysis extends Model
{
    protected $fillable = [
        'client_id',
        'type',
        'status',
        'input_version',
        'prompt_version',
        'model',
        'input_data',
        'result_json',
        'raw_response',
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
            'input_data' => 'array',
            'result_json' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
