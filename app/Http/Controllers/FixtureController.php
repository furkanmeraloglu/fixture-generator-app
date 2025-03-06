<?php

namespace App\Http\Controllers;

use App\Services\Fixture\GenerateLeagueFixturesService;
use App\Services\Fixture\ReadAllWeeklyFixturesService;
use App\Services\Fixture\ReadWeeklyFixtureService;
use App\Services\Fixture\ResetLeagueFixturesService;
use App\Services\Fixture\SimulateAllWeeksService;
use App\Services\Fixture\SimulateWeekService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FixtureController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllWeeklyFixtures(Request $request): JsonResponse
    {
        try {
            $data = (new ReadAllWeeklyFixturesService($request))->boot();
            return $this->getSuccessfulResponse($data, 'All weekly fixtures returned successfully');
        } catch (\Throwable $th) {
            return $this->getErrorResponse($th->getMessage(), $th->getCode());
        }
    }

    /**
     * @param Request $request
     * @param int $week
     * @return JsonResponse
     */
    public function getWeeklyFixture(Request $request, int $week): JsonResponse
    {
        try {
            $data = (new ReadWeeklyFixtureService($request, $week))->boot();
            return $this->getSuccessfulResponse($data, 'Weekly fixture returned successfully');
        } catch (\Throwable $th) {
            return $this->getErrorResponse($th->getMessage(), $th->getCode());
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function generateLeagueFixtures(Request $request): JsonResponse
    {
        try {
            $data = (new GenerateLeagueFixturesService())->boot();
            return $this->getSuccessfulResponse($data, 'League fixtures generated successfully', Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->getErrorResponse($th->getMessage(), $th->getCode());
        }
    }

    public function resetAllFixturesAndFootballMatchesAndReGenerateNewFixtures(Request $request): JsonResponse
    {
        try {
            $data = (new ResetLeagueFixturesService($request))->boot();
            return $this->getSuccessfulResponse($data, 'League fixtures has been reset successfully');
        } catch (\Throwable $th) {
            return $this->getErrorResponse($th->getMessage(), $th->getCode());
        }
    }

    public function simulateWeekMatches(Request $request): JsonResponse
    {
        try {
            $data = (new SimulateWeekService())->boot();
            return $this->getSuccessfulResponse([], $data['message']);
        } catch (\Throwable $th) {
            return $this->getErrorResponse($th->getMessage(), $th->getCode());
        }
    }

    public function simulateAllWeeksMatches(Request $request): JsonResponse
    {
        try {
            $data = (new SimulateAllWeeksService())->boot();
            return $this->getSuccessfulResponse([], $data['message']);
        } catch (\Throwable $th) {
            return $this->getErrorResponse($th->getMessage(), $th->getCode());
        }
    }
}
