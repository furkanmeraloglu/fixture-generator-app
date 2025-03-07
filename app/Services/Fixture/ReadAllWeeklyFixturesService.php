<?php

namespace App\Services\Fixture;

use App\Models\FootballMatch;
use Illuminate\Database\Eloquent\Collection;

class ReadAllWeeklyFixturesService
{
    protected Collection $fixtures;

    /**
     * @return array
     */
    public function boot(): array
    {
        return FootballMatch::query()->with([
            'homeTeam' => function ($query) {
                $query->select('team_id', 'name', 'points', 'goals_scored', 'goals_conceded');
            },
            'awayTeam' => function ($query) {
                $query->select('team_id', 'name', 'points', 'goals_scored', 'goals_conceded');
            }
        ])
            ->orderBy('week')
            ->get()
            ->groupBy('week')
            ->toArray();
    }
}
