<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}
