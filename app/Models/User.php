<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole(['admin', 'finance', 'warehouse']) || $this->role === 'admin';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'phone_verified_at',
        'role',
        'grade',
        'is_guest',
        'provider',
        'provider_id',
        'avatar',
        'status',
        'last_login_at',
        'premium_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
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
            'phone_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'premium_expires_at' => 'datetime',
            'password' => 'hashed',
            'is_guest' => 'boolean',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is regular user
     */
    public function isRegular(): bool
    {
        return $this->role === 'regular';
    }

    /**
     * Get user's orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get user's addresses
     */
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    /**
     * Get user's wallet
     */
    public function wallet(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(MemberWallet::class);
    }

    /**
     * Get user's balance ledgers
     */
    public function balanceLedgers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MemberBalanceLedger::class);
    }

    /**
     * Get user's premium memberships
     */
    public function premiumMemberships(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PremiumMembership::class);
    }

    /**
     * Get user's active premium membership
     */
    public function activePremiumMembership(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PremiumMembership::class)
            ->where('status', 'active')
            ->where('started_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Check if user is premium member
     */
    public function isPremium(): bool
    {
        return $this->premium_expires_at && $this->premium_expires_at->isFuture();
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
