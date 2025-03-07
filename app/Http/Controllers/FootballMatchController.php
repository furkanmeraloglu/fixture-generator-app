<?php

namespace App\Http\Controllers;

use App\Services\FootballMatch\UpdateMatchResultService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FootballMatchController extends Controller
{
    /**
     * @param Request $request
     * @param int $fixture_id
     * @return JsonResponse
     */
    public function updateMatchResult(Request $request, int $fixture_id): JsonResponse
    {
        try {
            $data = (new UpdateMatchResultService($request, $fixture_id))->boot();
            return $this->getSuccessfulResponse($data, 'The game scores updated successfully');
        } catch (\Throwable $th) {
            return $this->getErrorResponse($th->getMessage(), $th->getCode());
        }
    }
}
