<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentCourse extends Model
{
    protected $fillable = [
        'title',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
