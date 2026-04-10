<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SleepLog extends Model
{
    protected $table = 'sleep_log';
    // Mismo caso que BodyWeightLog — el nombre no sigue la convención plural

    protected $fillable = [
        'user_id',
        'date',
        'hours',
        'quality',
    ];

    protected $casts = [
        'date' => 'date',
        'hours' => 'decimal:1',
        // Mantiene 1 decimal al serializar: 7.5, 8.0, 6.5
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}