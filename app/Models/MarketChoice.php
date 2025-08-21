<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketChoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'market_id',
        'name',
        'party',
        'notes',
        'slug',
    ];

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class, 'choice_id');
    }
}


