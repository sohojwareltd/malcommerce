<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'affiliate_code',
        'sponsor_id',
        'address',
        'photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function sponsor()
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'sponsor_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'sponsor_id');
    }

    // Helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSponsor(): bool
    {
        return $this->role === 'sponsor';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Generate affiliate code for all users (all registered users are sponsors)
            if (empty($user->affiliate_code)) {
                // Generate unique 6-digit affiliate code
                do {
                    $code = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
                } while (User::where('affiliate_code', $code)->exists());
                
                $user->affiliate_code = $code;
            }
        });
    }
}
