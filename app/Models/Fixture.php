<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $fixture_id
 * @property int $week
 * @property bool $is_played
 * @property Carbon|string|null $created_at
 * @property Carbon|string|null $updated_at
 * @property Carbon|string|null $deleted_at
 */
class Fixture extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $primaryKey = 'fixture_id';
    protected $fillable = [
        'week',
        'is_played',
        'deleted_at'
    ];
    protected $casts = [
        'week' => 'integer',
        'is_played' => 'boolean',
        'deleted_at' => 'datetime'
    ];

    /**
     * @return HasMany
     */
    public function footballMatches(): HasMany
    {
        return $this->hasMany(FootballMatch::class, 'fixture_id', 'fixture_id');
    }
}
