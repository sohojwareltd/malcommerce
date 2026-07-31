<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'serial_number',
        'registration_number',
        'roll_number',
        'name',
        'father_name',
        'mother_name',
        'gender',
        'institute_id',
        'student_course_id',
        'session',
        'course_duration',
        'student_thana',
        'student_district',
        'examinee_type',
        'photo',
        'cgpa',
        'letter_grade',
        'exam_month',
        'issue_date',
        'marks_written',
        'marks_internship',
        'marks_viva',
        'marks_total',
        'marks_full',
    ];

    protected function casts(): array
    {
        return [
            'cgpa' => 'decimal:2',
            'issue_date' => 'date',
            'marks_written' => 'integer',
            'marks_internship' => 'integer',
            'marks_viva' => 'integer',
            'marks_total' => 'integer',
            'marks_full' => 'integer',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Student $student) {
            if (empty($student->serial_number)) {
                do {
                    $candidate = 'SN' . now()->format('Ymd') . str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                } while (self::where('serial_number', $candidate)->exists());

                $student->serial_number = $candidate;
            }
        });

        static::saving(function (Student $student) {
            if ($student->marks_written !== null || $student->marks_internship !== null || $student->marks_viva !== null) {
                $student->marks_total = (int) ($student->marks_written ?? 0)
                    + (int) ($student->marks_internship ?? 0)
                    + (int) ($student->marks_viva ?? 0);
            }
        });
    }

    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(StudentCourse::class, 'student_course_id');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        return str_starts_with($this->photo, 'http')
            ? $this->photo
            : asset('storage/' . $this->photo);
    }

    public function getGenderLabelAttribute(): string
    {
        return match ($this->gender) {
            'male' => 'Male',
            'female' => 'Female',
            default => 'Other',
        };
    }

    public function getExamineeTypeLabelAttribute(): string
    {
        return ucfirst($this->examinee_type ?? 'regular');
    }

    public function getVerifyUrlAttribute(): string
    {
        return route('student-documents.verify', $this->serial_number);
    }

    public function getQrCodeUrlAttribute(): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' . urlencode($this->verify_url);
    }

    public static function genderOptions(): array
    {
        return ['male' => 'Male', 'female' => 'Female', 'other' => 'Other'];
    }

    public static function examineeTypeOptions(): array
    {
        return ['regular' => 'Regular', 'irregular' => 'Irregular'];
    }
}
