<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institute extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_bn',
        'code',
        'address',
        'email',
        'website',
        'logo',
        'approval_text',
        'established_year',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Institute $institute) {
            if (empty($institute->code)) {
                do {
                    $candidate = 'INS' . str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                } while (self::where('code', $candidate)->exists());

                $institute->code = $candidate;
            }

            if ($institute->is_active === null) {
                $institute->is_active = true;
            }
        });
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        return str_starts_with($this->logo, 'http')
            ? $this->logo
            : asset('storage/' . $this->logo);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
