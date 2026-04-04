<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SponsorLevelHistory extends Model
{
    protected $table = 'sponsor_level_histories';

    protected $fillable = [
        'user_id',
        'from_sponsor_level_id',
        'to_sponsor_level_id',
        'changed_by',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fromLevel(): BelongsTo
    {
        return $this->belongsTo(SponsorLevel::class, 'from_sponsor_level_id');
    }

    public function toLevel(): BelongsTo
    {
        return $this->belongsTo(SponsorLevel::class, 'to_sponsor_level_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
