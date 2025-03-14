<?php

namespace App\Services\Fixture;

use App\Models\Fixture;
use App\Models\FootballMatch;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class GenerateLeagueFixturesService
{
    protected Collection|\Illuminate\Support\Collection|array $teamIds;
    protected array $fixtures = [];
    protected int $totalNumberOfWeeks;

    /**
     * @param bool $reset
     * @return array
     * @throws \Exception
     */
    public function boot(bool $reset = false): array
    {
        $this->getAllTeams($reset);
        $this->getTotalNumberOfWeeks();
        $this->generateFirstRoundOfFixtures();
        $this->generateSecondRoundOfFixtures();
        $this->saveFixturesAndMatchesToDB();
        return (new ReadAllWeeklyFixturesService())->boot();
    }

    /**
     * @param bool $reset
     * @return void
     * @throws \Exception
     */
    private function getAllTeams(bool $reset = false): void
    {
        $team_ids = Team::query()->pluck('team_id');

        if (count($team_ids) % 2 !== 0) {
            throw new \Exception('The league fixtures can only be generated for even number of teams');
        }

        if ($reset) {
            $this->teamIds = $team_ids->shuffle()->values()->toArray();
        } else {
            $this->teamIds = $team_ids->toArray();
        }
    }

    /**
     * @return void
     */
    private function getTotalNumberOfWeeks(): void
    {
        $this->totalNumberOfWeeks = (count($this->teamIds) - 1) * 2;
    }

    /**
     * @return void
     */
    private function generateFirstRoundOfFixtures(): void
    {
        for ($round = 0; $round < $this->totalNumberOfWeeks / 2; $round++) {
            $matches = [];

            for ($i = 0; $i < count($this->teamIds) / 2; $i++) {
                $home = $this->teamIds[$i];
                $away = $this->teamIds[count($this->teamIds) - 1 - $i];

                $matches[] = [
                    'home_team_id' => $home,
                    'away_team_id' => $away,
                    'week' => $round + 1,
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ];
            }

            array_splice($this->teamIds, 1, 0, array_pop($this->teamIds));

            $this->fixtures = array_merge($this->fixtures, $matches);
        }

    }

    /**
     * @return void
     */
    private function generateSecondRoundOfFixtures(): void
    {
        $secondRound = collect($this->fixtures)->map(function ($match) {
            return [
                'home_team_id' => $match['away_team_id'],
                'away_team_id' => $match['home_team_id'],
                'week' => $match['week'] + 3,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ];
        })->toArray();

        $this->fixtures = array_merge($this->fixtures, $secondRound);
    }

    /**
     * @return void
     */
    private function saveFixturesAndMatchesToDB(): void
    {
        foreach ($this->fixtures as $key => $fixture) {
            $currentWeekFixture = Fixture::query()->create([
                'week' => $fixture['week'],
                'is_played' => false,
                'created_at' => $fixture['created_at'],
                'updated_at' => $fixture['updated_at'],
            ]);

            FootballMatch::query()->create([
                'home_team_id' => $fixture['home_team_id'],
                'away_team_id' => $fixture['away_team_id'],
                'week' => $fixture['week'],
                'is_played' => false,
                'fixture_id' => $currentWeekFixture->fixture_id,
                'created_at' => $fixture['created_at'],
                'updated_at' => $fixture['updated_at'],
            ]);
        }
    }

}
