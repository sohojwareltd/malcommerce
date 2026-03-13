<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Trade extends Model
{
    protected $fillable = ['name', 'sort_order'];

    protected function casts(): array
    {
        return [];
    }

    public function venues(): BelongsToMany
    {
        return $this->belongsToMany(Venue::class, 'venue_trade')->withTimestamps();
    }

    public function workshopSeminars(): BelongsToMany
    {
        return $this->belongsToMany(WorkshopSeminar::class, 'workshop_seminar_trade')->withTimestamps();
    }
}
