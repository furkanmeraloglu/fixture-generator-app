<?php

namespace App\Services\FootballMatch;

use App\Models\ChampionshipPrediction;
use App\Models\FootballMatch;
use App\Models\Team;
use App\Traits\GenerateChampionshipPercentageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UpdateMatchResultService
{
    use GenerateChampionshipPercentageTrait;

    protected Request $request;
    protected int $fixture_id;
    protected ?FootballMatch $footballMatch;
    protected ?Team $homeTeam;
    protected ?Team $awayTeam;

    /**
     * @param Request $request
     * @param int $fixture_id
     */
    public function __construct(Request $request, int $fixture_id)
    {
        $this->request = $request;
        $this->fixture_id = $fixture_id;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function boot(): array
    {
        try {
            DB::beginTransaction();
            $this->findFootballMatch();
            $this->findHomeAndAwayTeams();
            $this->updateTeamStatistics();
            $this->updateFootballMatchResult();
            $this->updateChampionshipPredictionIfNecessary();
            DB::commit();
            return ['message' => 'The football match has been updated successfully!'];
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new \Exception('The football match could not be updated!');
        }
    }

    /**
     * @return void
     * @throws \Exception
     */
    private function findFootballMatch(): void
    {
        $this->footballMatch = FootballMatch::query()->where('fixture_id', $this->fixture_id)->first();
        if (!$this->footballMatch->is_played) {
            throw new \Exception('You can only update the result of a played match');
        }
    }

    /**
     * @return void
     */
    private function findHomeAndAwayTeams(): void
    {
        $teams = Team::query()->whereIn('team_id', [$this->footballMatch->home_team_id, $this->footballMatch->away_team_id])->get();
        $this->homeTeam = $teams->where('team_id', $this->footballMatch->home_team_id)->first();
        $this->awayTeam = $teams->where('team_id', $this->footballMatch->away_team_id)->first();
    }

    /**
     * @return void
     */
    private function updateTeamStatistics(): void
    {
        $previous_home_goals = $this->footballMatch->home_team_goals;
        $previous_away_goals = $this->footballMatch->away_team_goals;
        $new_home_goals = $this->request->home_team_goals;
        $new_away_goals = $this->request->away_team_goals;

        if ($previous_home_goals > $previous_away_goals) {
            $this->homeTeam->wins -= 1;
            $this->homeTeam->points -= 3;
            $this->awayTeam->losses -= 1;
        } else if ($previous_home_goals < $previous_away_goals) {
            $this->awayTeam->wins -= 1;
            $this->awayTeam->points -= 3;
            $this->homeTeam->losses -= 1;
        } else {
            $this->homeTeam->draws -= 1;
            $this->homeTeam->points -= 1;
            $this->awayTeam->draws -= 1;
            $this->awayTeam->points -= 1;
        }

        if ($this->request->home_team_goals > $this->request->away_team_goals) {
            $this->homeTeam->wins += 1;
            $this->homeTeam->points += 3;
            $this->awayTeam->losses += 1;
        } else if ($this->request->home_team_goals < $this->request->away_team_goals) {
            $this->awayTeam->wins += 1;
            $this->awayTeam->points += 3;
            $this->homeTeam->losses += 1;
        } else {
            $this->homeTeam->draws += 1;
            $this->homeTeam->points += 1;
            $this->awayTeam->draws += 1;
            $this->awayTeam->points += 1;
        }

        $this->homeTeam->goals_scored -= $previous_home_goals;
        $this->homeTeam->goals_conceded -= $previous_away_goals;
        $this->awayTeam->goals_scored -= $previous_away_goals;
        $this->awayTeam->goals_conceded -= $previous_home_goals;

        $this->homeTeam->goals_scored += $new_home_goals;
        $this->homeTeam->goals_conceded += $new_away_goals;
        $this->awayTeam->goals_scored += $new_away_goals;
        $this->awayTeam->goals_conceded += $new_home_goals;

        $this->homeTeam->save();
        $this->awayTeam->save();
    }

    /**
     * @return void
     */
    private function updateFootballMatchResult(): void
    {
        $this->footballMatch->update([
            'home_team_goals' => $this->request->home_team_goals,
            'away_team_goals' => $this->request->away_team_goals,
            'is_played' => true
        ]);
    }

    /**
     * @return void
     */
    private function updateChampionshipPredictionIfNecessary(): void
    {
        if ($this->footballMatch->week >= 4 && $this->footballMatch->week !== 6) {
            $predictions = $this->generateChampionshipPredictionIfRequired();
            ChampionshipPrediction::query()->whereNull('deleted_at')->where('week', $this->footballMatch->week)->update([
                'week' => $this->footballMatch->week,
                'predictions' => $predictions
            ]);
        }
    }
}
