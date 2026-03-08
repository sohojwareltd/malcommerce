<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    protected $fillable = [
        'job_circular_id',
        'name',
        'email',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'education',
        'experience',
        'resume_path',
        'cover_letter',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'education' => 'array',
            'experience' => 'array',
            'date_of_birth' => 'date',
        ];
    }

    public function jobCircular(): BelongsTo
    {
        return $this->belongsTo(JobCircular::class);
    }
}
