<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

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
        'sponsor_level_id',
        'created_from_order_id',
        'address',
        'photo',
        'comment',
        'balance',
        'withdrawal_methods',
        'default_withdrawal_method',
        'minimum_withdrawal_limit',
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
            'balance' => 'decimal:2',
            'withdrawal_methods' => 'array',
            'minimum_withdrawal_limit' => 'decimal:2',
        ];
    }

    // Relationships
    public function sponsor()
    {
        return $this->belongsTo(User::class, 'sponsor_id')->withTrashed();
    }

    public function sponsorLevel()
    {
        return $this->belongsTo(SponsorLevel::class, 'sponsor_level_id');
    }

    public function sponsorLevelHistories()
    {
        return $this->hasMany(SponsorLevelHistory::class)->orderByDesc('created_at');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'sponsor_id');
    }

    public function createdFromOrder()
    {
        return $this->belongsTo(Order::class, 'created_from_order_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'sponsor_id');
    }

    public function customerOrders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function earnings()
    {
        return $this->hasMany(Earning::class, 'sponsor_id');
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'sponsor_id');
    }

    public function galleryPhotos()
    {
        return $this->hasMany(GalleryPhoto::class);
    }

    public function purchasesSubmitted()
    {
        return $this->hasMany(Purchase::class, 'submitted_by_sponsor_id');
    }

    public function purchasesAsBeneficiary()
    {
        return $this->hasMany(Purchase::class, 'beneficiary_user_id');
    }

    public function sponsorIncomes()
    {
        return $this->hasMany(SponsorIncome::class, 'sponsor_id')->orderByDesc('created_at');
    }

    public function uploadedGalleryPhotos()
    {
        return $this->hasMany(GalleryPhoto::class, 'uploaded_by_id');
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
            // Generate affiliate code only for sponsors (not for admin users)
            if (empty($user->affiliate_code) && $user->role !== 'admin') {
                // Generate unique 6-digit affiliate code
                do {
                    $code = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
                } while (User::where('affiliate_code', $code)->exists());
                
                $user->affiliate_code = $code;
            }
        });
    }
}
