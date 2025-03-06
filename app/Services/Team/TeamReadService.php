<?php

namespace App\Services\Team;

use App\Exceptions\DataNotFoundException;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TeamReadService
{
    protected int $teamId;

    /**
     * @param int $team_id
     */
    public function __construct(int $team_id)
    {
        $this->teamId = $team_id;
    }

    /**
     * @return array
     * @throws DataNotFoundException
     */
    public function boot(): array
    {
        $team = Team::query()->find($this->teamId);
        if (blank($team)) {
            throw new DataNotFoundException('Team could not be found!');
        }
        return $team->append('team_all_matches')->toArray();
    }
}
