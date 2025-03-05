<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $fixture_id
 * @property int $week
 * @property bool $is_played
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
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
}
