<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @property int $football_match_id
 * @property int $home_team_id
 * @property int $away_team_id
 * @property int $home_team_goals
 * @property int $away_team_goals
 * @property int $fixture_id
 * @property int $week
 * @property bool $is_played
 * @property Carbon|string|null $created_at
 * @property Carbon|string|null $updated_at
 * @property Carbon|string|null $deleted_at
 */
class FootballMatch extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $primaryKey = 'football_match_id';
    protected $fillable = [
        'home_team_id',
        'away_team_id',
        'home_team_goals',
        'away_team_goals',
        'fixture_id',
        'week',
        'is_played',
        'deleted_at'
    ];

    protected $casts = [
        'home_team_id' => 'integer',
        'away_team_id' => 'integer',
        'home_team_goals' => 'integer',
        'away_team_goals' => 'integer',
        'fixture_id' => 'integer',
        'week' => 'integer',
        'is_played' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * @return BelongsTo
     */
    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id', 'team_id');
    }

    /**
     * @return BelongsTo
     */
    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id', 'team_id');
    }

    /**
     * @return BelongsTo
     */
    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class, 'fixture_id', 'fixture_id');
    }
}
