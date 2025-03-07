<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @property int $championship_prediction_id
 * @property int $week
 * @property array $redictions
 * @property Carbon|string|null $created_at
 * @property Carbon|string|null $updated_at
 * @property Carbon|string|null $deleted_at
 */
class ChampionshipPrediction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $primaryKey = 'championship_prediction_id';
    protected $fillable = [
        'predictions',
        'week',
        'deleted_at'
    ];
    protected $casts = [
        'predictions' => 'array',
        'week' => 'integer',
        'deleted_at' => 'datetime',
    ];
}
