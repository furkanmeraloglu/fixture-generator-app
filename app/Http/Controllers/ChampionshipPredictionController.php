<?php

namespace App\Http\Controllers;

use App\Services\ChampionshipPrediction\ReadChampionshipPredictionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChampionshipPredictionController extends Controller
{
    /**
     * @param Request $request
     * @param int $week
     * @return JsonResponse
     */
    public function getWeekPredictions(Request $request, int $week): JsonResponse
    {
        try {
            $data = (new ReadChampionshipPredictionService())->boot($week);
            return $this->getSuccessfulResponse($data, 'League fixtures has been reset successfully');
        } catch (\Throwable $th) {
            return $this->getErrorResponse($th->getMessage(), $th->getCode());
        }
    }
}
