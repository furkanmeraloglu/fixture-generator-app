<?php

namespace App\Services\Fixture;

use App\Exceptions\DataNotFoundException;
use App\Models\Fixture;
use Illuminate\Http\Request;

class ReadWeeklyFixtureService
{
    protected Request $request;
    protected int $week;

    /**
     * @param Request $request
     * @param int $week
     */
    public function __construct(Request $request, int $week)
    {
        $this->request = $request;
        $this->week = $week;
    }

    /**
     * @return array
     * @throws DataNotFoundException
     */
    public function boot(): array
    {
        $fixture = Fixture::query()->with(
            [
                'footballMatches' => function ($query) {
                    $query->select('fixture_id', 'home_team_id', 'away_team_id', 'home_team_goals', 'away_team_goals');
                },
                'footballMatches.homeTeam' => function ($query) {
                    $query->select('team_id', 'name', 'points', 'goals_scored', 'goals_conceded', 'wins', 'losses', 'draws', 'played_matches');
                },
                'footballMatches.awayTeam' => function ($query) {
                    $query->select('team_id', 'name', 'points', 'goals_scored', 'goals_conceded', 'wins', 'losses', 'draws', 'played_matches');
                }
            ])->where('week', $this->week)->get();

        if (blank($fixture)) {
            throw new DataNotFoundException('Fixture could not be found!');
        }

        return $fixture->toArray();
    }
}
