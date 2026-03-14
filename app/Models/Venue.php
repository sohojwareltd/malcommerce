<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Venue extends Model
{
    protected $fillable = ['name', 'address', 'sort_order'];

    protected function casts(): array
    {
        return [];
    }

    public function trades(): BelongsToMany
    {
        return $this->belongsToMany(Trade::class, 'venue_trade')->withTimestamps();
    }

    public function workshopSeminars(): BelongsToMany
    {
        return $this->belongsToMany(WorkshopSeminar::class, 'workshop_seminar_venue')->withTimestamps();
    }
}
