<?php

namespace App\Traits;

use App\Models\FootballMatch;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

trait GenerateChampionshipPercentageTrait
{
    use SimulateFootballMarchTrait;

    protected Collection $teams;
    protected array $upcomingMatchResultPredictions = [];
    protected int $upcomingMatchesTotalPredictedPointsOfAllTeams = 0;
    protected Collection $allRemainingMatchesOfTheLeague;
    protected array $teamsWithNoChampionshipChance = [];

    /**
     * @return array
     */
    public function generateChampionshipPredictionIfRequired(): array
    {
        $this->getAllTeams();
        $this->getAllRemainingMatches();
        $this->calculateTheProbabilityOfTeamsCannotBeChampion();
        $this->calculateChampionshipProbabilityOfTeamsExceptTeamsWithNoChampionshipChance();

        usort($this->upcomingMatchResultPredictions, function ($a, $b) {
            return $b['predicted_points'] <=> $a['predicted_points'];
        });

        $leaderPoints = $this->upcomingMatchResultPredictions[0]['predicted_points'];
        $totalPercentage = 0;

        foreach ($this->upcomingMatchResultPredictions as &$prediction) {
            $prediction['championship_percentage'] = ($prediction['predicted_points'] / $this->upcomingMatchesTotalPredictedPointsOfAllTeams) * 100;
            $totalPercentage += $prediction['championship_percentage'];
        }

        if ($totalPercentage > 0) {
            foreach ($this->upcomingMatchResultPredictions as &$prediction) {
                if ($prediction['championship_percentage'] > 0) {
                    $prediction['championship_percentage'] = ($prediction['championship_percentage'] / $totalPercentage) * 100;
                }
            }
        }

        if (count($this->allRemainingMatchesOfTheLeague) == 1 &&
            $leaderPoints > $this->upcomingMatchResultPredictions[1]['predicted_points']) {
            $this->upcomingMatchResultPredictions[0]['championship_percentage'] = 100;
            for ($i = 1; $i < count($this->upcomingMatchResultPredictions); $i++) {
                $this->upcomingMatchResultPredictions[$i]['championship_percentage'] = 0;
            }
        }

        return $this->formatResponse();
    }

    /**
     * @return void
     */
    protected function getAllTeams(): void
    {
        $this->teams = Team::query()->orderBy('points')->orderBy('name')->get();
    }

    /**
     * @return void
     */
    protected function getAllRemainingMatches(): void
    {
        $this->allRemainingMatchesOfTheLeague = FootballMatch::query()
            ->where('is_played', false)
            ->get();
    }

    /**
     * @return void
     */
    protected function calculateTheProbabilityOfTeamsCannotBeChampion(): void
    {
        foreach ($this->teams as $team) {
            if ($team->points + (3 * ($this->allRemainingMatchesOfTheLeague->count() / 2)) < $this->teams->sortByDesc('points')->first()->points) {
                $this->teamsWithNoChampionshipChance[] = [
                    'team_id' => $team->team_id,
                    'team' => $team->name,
                    'points' => $team->points,
                ];
            }
        }
    }

    /**
     * @return void
     */
    protected function calculateChampionshipProbabilityOfTeamsExceptTeamsWithNoChampionshipChance(): void
    {
        foreach ($this->teams as $team) {
            if (in_array($team->team_id, array_column($this->teamsWithNoChampionshipChance, 'team_id'))) {
                continue;
            }
            $predicted_points_of_team = $team->points;
            foreach ($this->allRemainingMatchesOfTheLeague as $match) {
                $home_team = $match->homeTeam;
                $away_team = $match->awayTeam;
                $match_result = $this->simulateFootballMatch($match);
                if ($team->team_id == $home_team->team_id || $team->team_id == $away_team->team_id) {
                    if ($match_result['home_score'] > $match_result['away_score']) {
                        $predicted_points_of_team += ($team->team_id == $home_team->team_id) ? 3 : 0;
                    } elseif ($match_result['home_score'] < $match_result['away_score']) {
                        $predicted_points_of_team += ($team->team_id == $away_team->team_id) ? 3 : 0;
                    } else {
                        $predicted_points_of_team += 1;
                    }
                }
            }
            $this->upcomingMatchResultPredictions[] = [
                'team' => $team->name,
                'predicted_points' => $predicted_points_of_team,
            ];
            $this->upcomingMatchesTotalPredictedPointsOfAllTeams += $predicted_points_of_team;
        }
    }

    /**
     * @return array
     */
    protected function formatResponse(): array
    {
        $data = [];
        foreach ($this->upcomingMatchResultPredictions as $prediction) {
            $data[] = [
                'team' => $prediction['team'],
                'championship_percentage' => $prediction['championship_percentage'],
            ];
        }
        if (!blank($this->teamsWithNoChampionshipChance)) {
            foreach ($this->teamsWithNoChampionshipChance as $team) {
                $data[] = [
                    'team' => $team['team'],
                    'championship_percentage' => 0,
                ];
            }
        }
        return $data;
    }
}
