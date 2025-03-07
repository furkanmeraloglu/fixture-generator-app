<?php

namespace App\Services\Team;

use App\Models\Team;

class TeamReadAllService
{
    /**
     * @return array
     */
    public function boot(): array
    {
        return Team::query()
            ->get()
            ->sortBy([
                ['points', 'desc'],
                ['team_total_score_average', 'desc'],
                ['name', 'asc']
            ])
            ->values()
            ->toArray();
    }
}
