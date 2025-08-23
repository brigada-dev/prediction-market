<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'balance',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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
            'role' => 'string',
        ];
    }

    /**
     * Get the positions for the user.
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    /**
     * Get the prize claims for the user.
     */
    public function prizeClaims(): HasMany
    {
        return $this->hasMany(PrizeClaim::class);
    }

    /**
     * Get the token purchases for the user.
     */
    public function tokenPurchases(): HasMany
    {
        return $this->hasMany(TokenPurchase::class);
    }

    /**
     * Get the user's initials derived from their name.
     */
    public function initials(): string
    {
        $name = trim((string) $this->name);
        if ($name === '') {
            return '';
        }

        $parts = preg_split('/\s+/', $name) ?: [];
        $firstInitial = $parts !== [] ? mb_substr($parts[0], 0, 1) : '';
        $lastInitial = count($parts) > 1 ? mb_substr($parts[count($parts) - 1], 0, 1) : '';

        return mb_strtoupper($firstInitial . $lastInitial);
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a normal user.
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Get user stats for admin dashboard.
     */
    public function getStatsAttribute(): array
    {
        return [
            'total_positions' => $this->positions()->count(),
            'total_invested' => $this->positions()->sum('cost'),
            'total_shares' => $this->positions()->sum('shares'),
            'active_markets' => $this->positions()->distinct('market_id')->count('market_id'),
            'profit_loss' => $this->calculateProfitLoss(),
        ];
    }

    /**
     * Calculate user's current profit/loss.
     */
    private function calculateProfitLoss(): float
    {
        $totalCost = (float) $this->positions()->sum('cost');
        $currentValue = 0;

        foreach ($this->positions as $position) {
            $market = $position->market;
            if ($market->resolved) {
                // Market is resolved - calculate actual payout
                if ($market->outcome === $position->choice || 
                    ($position->marketChoice && $market->outcome === $position->marketChoice->slug)) {
                    $currentValue += $position->shares; // Winner gets $1 per share
                }
                // Losers get $0
            } else {
                // Market is active - use current market price
                $marketMaker = app(\App\Services\MarketMaker::class);
                $prices = $marketMaker->price($market);
                
                if ($position->marketChoice) {
                    $currentPrice = $prices[$position->marketChoice->slug] ?? 0;
                } else {
                    $currentPrice = $prices[$position->choice] ?? 0;
                }
                
                $currentValue += $position->shares * $currentPrice;
            }
        }

        return round($currentValue - $totalCost, 2);
    }
}
