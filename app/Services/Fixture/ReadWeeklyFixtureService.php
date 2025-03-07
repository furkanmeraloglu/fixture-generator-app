<?php

namespace App\Services\Fixture;

use App\Exceptions\DataNotFoundException;
use App\Models\Fixture;
use App\Models\FootballMatch;
use Illuminate\Http\Request;

class ReadWeeklyFixtureService
{
    protected int $week;

    /**
     * @param int $week
     */
    public function __construct(int $week)
    {
        $this->week = $week;
    }

    /**
     * @return array
     * @throws DataNotFoundException
     */
    public function boot(): array
    {
        $fixture = FootballMatch::query()->with(
            [
                'homeTeam' => function ($query) {
                    $query->select('team_id', 'name', 'points', 'goals_scored', 'goals_conceded', 'wins', 'losses', 'draws', 'played_matches');
                },
                'awayTeam' => function ($query) {
                    $query->select('team_id', 'name', 'points', 'goals_scored', 'goals_conceded', 'wins', 'losses', 'draws', 'played_matches');
                }
            ])->where('week', $this->week)->get();

        if (blank($fixture)) {
            throw new DataNotFoundException('Week fixture could not be found!');
        }

        return $fixture->toArray();
    }
}
