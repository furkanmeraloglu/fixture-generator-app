<?php

namespace App\Services\ChampionshipPrediction;

use App\Exceptions\DataNotFoundException;
use App\Models\ChampionshipPrediction;

class ReadChampionshipPredictionService
{
    public function boot(int $week): array
    {
        $predictions = ChampionshipPrediction::query()
            ->select('week', 'predictions')
            ->where('week', $week)
            ->whereNull('deleted_at')
            ->first();
        if (blank($predictions)) {
            throw new DataNotFoundException('No predictions found for this week');
        }
        return $predictions->toArray();
    }
}
