<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopEnrollment extends Model
{
    protected $fillable = [
        'workshop_seminar_id',
        'venue_id',
        'trade_id',
        'name',
        'phone',
        'address',
        'notes',
        'status',
    ];

    public function workshopSeminar(): BelongsTo
    {
        return $this->belongsTo(WorkshopSeminar::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Venue::class);
    }

    public function trade(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Trade::class);
    }
}
