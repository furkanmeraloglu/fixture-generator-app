<?php

namespace App\Services\Fixture;

use App\Models\FootballMatch;
use App\Traits\SimulateFootballMatchTrait;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class SimulateAllWeeksService extends SimulateWeekService
{
    use SimulateFootballMatchTrait;

    /**
     * @param int|null $week
     * @return string[]
     * @throws Exception
     */
    public function boot(?int $week = null): array
    {
        $this->getAllUnPlayedFootballMatches();
        if (blank($this->footballMatches)) {
            throw new Exception('All football matches of this season have been played. Please reset the season to simulate again.', Response::HTTP_BAD_REQUEST);
        }
        foreach ($this->footballMatches as $football_match) {
            $this->footballMatchResult = $this->simulateFootballMatch($football_match, false);
            $this->updateFootballMatchResult();
            $this->updateEachTeamStats();
        }
        return (new ReadAllWeeklyFixturesService())->boot();
    }

    /**
     * @return void
     */
    private function getAllUnPlayedFootballMatches(): void
    {
        $this->footballMatches = FootballMatch::query()
            ->where('is_played', false)
            ->orderBy('week')
            ->get();
    }
}
