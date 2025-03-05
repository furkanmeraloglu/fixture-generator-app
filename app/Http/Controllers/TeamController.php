<?php

namespace App\Http\Controllers;

use App\Services\Team\TeamReadAllService;
use App\Services\Team\TeamReadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllTeams(Request $request): JsonResponse
    {
        try {
            $data = (new TeamReadAllService($request))->boot();
            return $this->getSuccessfulResponse($data, 'Teams returned successfully');
        } catch (\Throwable $th) {
            return $this->getErrorResponse($th->getMessage(), $th->getCode());
        }
    }

    /**
     * @param Request $request
     * @param int $team_id
     * @return JsonResponse
     */
    public function getTeam(Request $request, int $team_id): JsonResponse
    {
        try {
            $data = (new TeamReadService($request, $team_id))->boot();
            return $this->getSuccessfulResponse($data, 'Team returned successfully');
        } catch (\Throwable $th) {
            return $this->getErrorResponse($th->getMessage(), $th->getCode());
        }
    }
}
