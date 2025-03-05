<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $team_id
 * @property string $name
 * @property int $strength
 * @property int $points
 * @property int $goals_scored
 * @property int $goals_conceded
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Team extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $primaryKey = 'team_id';
    protected $fillable = [
        'name',
        'strength',
        'points',
        'goals_scored',
        'goals_conceded',
        'deleted_at'
    ];

    protected $casts = [
        'name' => 'string',
        'strength' => 'integer',
        'points' => 'integer',
        'goals_scored' => 'integer',
        'goals_conceded' => 'integer',
        'deleted_at' => 'datetime',
    ];

    protected $appends = [
        'team_all_matches',
        'team_total_score_average'
    ];

    /**
     * @return HasMany
     */
    public function homeMatches(): HasMany
    {
        return $this->hasMany(FootballMatch::class, 'home_team_id', 'team_id');
    }

    /**
     * @return HasMany
     */
    public function awayMatches(): HasMany
    {
        return $this->hasMany(FootballMatch::class, 'away_team_id', 'team_id');
    }

    /**
     * @return array
     */
    public function getTeamAllMatchesAttribute(): array
    {
        return $this->homeMatches
            ->merge($this->awayMatches)
            ->toArray();
    }

    /**
     * @return int
     */
    public function getTeamTotalScoreAverageAttribute(): int
    {
        return $this->goals_scored - $this->goals_conceded;
    }
}
