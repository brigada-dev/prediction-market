<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Market extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'closes_at',
        'resolved',
        'outcome',
        'liquidity',
        'b',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'closes_at' => 'datetime',
            'resolved' => 'boolean',
            'liquidity' => 'decimal:2',
            'b' => 'decimal:2',
        ];
    }

    /**
     * Get the positions for the market.
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    /**
     * Get yes positions for the market.
     */
    public function yesPositions(): HasMany
    {
        return $this->positions()->where('choice', 'yes');
    }

    /**
     * Get no positions for the market.
     */
    public function noPositions(): HasMany
    {
        return $this->positions()->where('choice', 'no');
    }

    /**
     * Check if the market is still open for trading.
     */
    public function isOpen(): bool
    {
        return !$this->resolved && $this->closes_at->isFuture();
    }

    /**
     * Check if the market is closed.
     */
    public function isClosed(): bool
    {
        return !$this->isOpen();
    }
}
