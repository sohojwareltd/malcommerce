<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use SoftDeletes;

    public const ACCESS_FREE = 'free';

    public const ACCESS_PAID = 'paid';

    public const ACCESS_CODE = 'code';

    public const ACCESS_PAID_OR_CODE = 'paid_or_code';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'access_type',
        'price',
        'pass_mark',
        'max_exam_attempts',
        'max_code_attempts',
        'duration_minutes',
        'certificate_enabled',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'pass_mark' => 'integer',
            'max_exam_attempts' => 'integer',
            'max_code_attempts' => 'integer',
            'duration_minutes' => 'integer',
            'certificate_enabled' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ExamQuestion::class)->orderBy('sort_order');
    }

    public function accessCodes(): HasMany
    {
        return $this->hasMany(ExamAccessCode::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(ExamEnrollment::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(ExamOrder::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(ExamCertificate::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isFree(): bool
    {
        return $this->access_type === self::ACCESS_FREE;
    }

    public function requiresPayment(): bool
    {
        return in_array($this->access_type, [self::ACCESS_PAID, self::ACCESS_PAID_OR_CODE], true)
            && (float) $this->price > 0;
    }

    public function requiresCode(): bool
    {
        return in_array($this->access_type, [self::ACCESS_CODE, self::ACCESS_PAID_OR_CODE], true);
    }

    public function allowsFreeStart(): bool
    {
        return $this->access_type === self::ACCESS_FREE;
    }

    public static function accessTypeLabels(): array
    {
        return [
            self::ACCESS_FREE => 'Free',
            self::ACCESS_PAID => 'Paid only',
            self::ACCESS_CODE => 'Access code only',
            self::ACCESS_PAID_OR_CODE => 'Paid or access code',
        ];
    }
}
