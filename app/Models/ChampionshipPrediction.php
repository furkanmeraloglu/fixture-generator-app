<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @property int $championship_prediction_id
 * @property int $team_id
 * @property int $week
 * @property float $probability_percentage
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class ChampionshipPrediction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $primaryKey = 'championship_prediction_id';
    protected $fillable = [
        'team_id',
        'week',
        'probability_percentage',
        'deleted_at'
    ];
    protected $casts = [
        'team_id' => 'integer',
        'week' => 'integer',
        'probability_percentage' => 'decimal:5,2'
    ];
}
