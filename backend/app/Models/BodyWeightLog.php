<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BodyWeightLog extends Model
{
    protected $table = 'body_weight_log';
    // Necesario porque Laravel asumiría "body_weight_logs" por convención
    // pero tu tabla se llama "body_weight_log" sin la 's' final

    protected $fillable = [
        'user_id',
        'date',
        'weight_kg',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}