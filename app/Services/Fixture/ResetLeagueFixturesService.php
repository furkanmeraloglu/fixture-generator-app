<?php

namespace App\Services\Fixture;

use App\Models\ChampionshipPrediction;
use App\Models\Fixture;
use App\Models\FootballMatch;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResetLeagueFixturesService
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function boot(): array
    {
        try {
            DB::beginTransaction();
            $this->deleteAllFootballMatchData();
            $this->deleteAllFixtureData();
            $this->resetAllChampionshipProbabilityRecords();
            $this->deleteAllTeamStatsAndRegenerate();
            DB::commit();
            return [ 'message' => 'All fixtures and related data have been reset successfully' ];
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception('The fixture and matches could not be reset!', $exception->getCode());
        }
    }

    /**
     * @return void
     */
    private function deleteAllFootballMatchData(): void
    {
        FootballMatch::query()->update(['deleted_at' => now()->toDateTimeString()]);
    }

    /**
     * @return void
     */
    private function deleteAllFixtureData(): void
    {
        Fixture::query()->update(['deleted_at' => now()->toDateTimeString()]);
    }

    /**
     * @return void
     */
    private function resetAllChampionshipProbabilityRecords(): void
    {
        ChampionshipPrediction::query()->update(['deleted_at' => now()->toDateTimeString()]);
    }

    /**
     * @return void
     */
    private function deleteAllTeamStatsAndRegenerate(): void
    {
        Team::query()->update([
            'points' => 0,
            'goals_scored' => 0,
            'goals_conceded' => 0
        ]);
    }
}
