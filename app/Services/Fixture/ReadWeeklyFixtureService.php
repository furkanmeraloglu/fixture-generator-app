<?php

namespace App\Services\Fixture;

use App\Exceptions\DataNotFoundException;
use App\Models\Fixture;
use Illuminate\Http\Request;

class ReadWeeklyFixtureService
{
    protected Request $request;
    protected int $fixtureId;

    /**
     * @param Request $request
     * @param int $fixture_id
     */
    public function __construct(Request $request, int $fixture_id)
    {
        $this->request = $request;
        $this->fixtureId = $fixture_id;
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
                    $query->select('team_id', 'name', 'points', 'goals_scored', 'goals_conceded');
                },
                'footballMatches.awayTeam' => function ($query) {
                    $query->select('team_id', 'name', 'points', 'goals_scored', 'goals_conceded');
                }
            ])->find($this->fixtureId);

        if (blank($fixture)) {
            throw new DataNotFoundException('Fixture could not be found!');
        }

        return $fixture->toArray();
    }
}
