<?php

namespace App\Services\Fixture;

use App\Models\Fixture;
use App\Models\FootballMatch;
use App\Models\Team;
use App\Traits\GenerateChampionshipPercentageTrait;
use App\Traits\SimulateFootballMarchTrait;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;

class SimulateWeekService
{
    use GenerateChampionshipPercentageTrait;
    use SimulateFootballMarchTrait;
    protected bool $isChampionshipPredictionRequired = false;
    protected Collection $footballMatches;
    protected array $footballMatchResult = [];

    /**
     * @return array
     * @throws Exception
     */
    public function boot(): array
    {
        $this->getFootballMatchesOfTheWeekByOrder();

        foreach ($this->footballMatches as $football_match) {
            $this->footballMatchResult = $this->simulateFootballMatch($football_match);
            $this->updateFootballMatchResult();
            $this->updateEachTeamStats();
        }
        $this->calculateChampionshipPredictionIfRequired();
        return ['message' => 'All football matches of the week have been simulated successfully'];
    }

    private function getFootballMatchesOfTheWeekByOrder(): void
    {
        $fixture = Fixture::query()
            ->select('week')
            ->where('is_played', false)
            ->orderBy('week')
            ->first();

        if (blank($fixture)) {
            throw new Exception('All football matches of this season have been played. Please reset the season to simulate again.', Response::HTTP_BAD_REQUEST);
        }

        $this->footballMatches = FootballMatch::query()
            ->where('week', $fixture->week)
            ->where('is_played', false)
            ->orderBy('created_at')
            ->get();
    }

    protected function updateFootballMatchResult(): void
    {
        $this->footballMatch->update([
            'home_team_goals' => $this->footballMatchResult['home_score'],
            'away_team_goals' => $this->footballMatchResult['away_score'],
            'is_played' => true
        ]);
        Fixture::query()->where('week', $this->footballMatch->week)->update(['is_played' => true]);
    }

    protected function updateEachTeamStats(): void
    {
        if ($this->footballMatchResult['home_score'] > $this->footballMatchResult['away_score']) {
            $this->updateTeamScoresAndPoints($this->homeTeam, 3, $this->footballMatchResult['home_score'], $this->footballMatchResult['away_score'], true, false, false);
            $this->updateTeamScoresAndPoints($this->awayTeam, 0, $this->footballMatchResult['away_score'], $this->footballMatchResult['home_score'], false, true, false);
        } else if ($this->footballMatchResult['home_score'] < $this->footballMatchResult['away_score']) {
            $this->updateTeamScoresAndPoints($this->homeTeam, 0, $this->footballMatchResult['home_score'], $this->footballMatchResult['away_score'], false, true, false);
            $this->updateTeamScoresAndPoints($this->awayTeam, 3, $this->footballMatchResult['away_score'], $this->footballMatchResult['home_score'], true, false, false);
        } else {
            $this->updateTeamScoresAndPoints($this->homeTeam, 1, $this->footballMatchResult['home_score'], $this->footballMatchResult['away_score'], false, false, true);
            $this->updateTeamScoresAndPoints($this->awayTeam, 1, $this->footballMatchResult['away_score'], $this->footballMatchResult['home_score'], false, false, true);
        }
    }

    private function updateTeamScoresAndPoints(Team $team, int $points, int $goals_scored, int $goals_conceded, bool $win, bool $loss, bool $draw): void
    {
        $team->update([
            'points' => $team->points + $points,
            'goals_scored' => $team->goals_scored + $goals_scored,
            'goals_conceded' => $team->goals_conceded + $goals_conceded,
            'wins' => ($win) ? $team->wins + 1 : $team->wins,
            'losses' => ($loss) ? $team->losses + 1 : $team->losses,
            'draws' => ($draw) ? $team->draws + 1 : $team->draws,
            'played_matches' => $team->played_matches + 1
        ]);
    }

    protected function calculateChampionshipPredictionIfRequired(bool $from_simulate_all_service = false): void
    {
        if ($this->isChampionshipPredictionRequired || $from_simulate_all_service) {
            dump('Championship prediction is required');
//            $this->generateChampionshipPredictionIfRequired();
        }
    }
}
