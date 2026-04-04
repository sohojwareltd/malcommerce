<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SponsorLevel extends Model
{
    protected $fillable = [
        'name',
        'rank',
        'commission_percent',
        'is_default_for_new',
    ];

    protected function casts(): array
    {
        return [
            'commission_percent' => 'decimal:2',
            'is_default_for_new' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'sponsor_level_id');
    }

    public static function defaultForNewSponsors(): ?self
    {
        return static::query()->where('is_default_for_new', true)->first();
    }
}
