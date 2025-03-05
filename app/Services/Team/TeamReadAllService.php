<?php

namespace App\Services\Team;

use App\Models\Team;
use App\Traits\ReadServicesTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TeamReadAllService
{
    use ReadServicesTrait;

    protected Request $request;
    protected Builder $teamsQuery;
    protected Team $serviceModelInstance;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->serviceModelInstance = new Team();
    }

    /**
     * @return array
     */
    public function boot(): array
    {
        $this->initializeTeamsQuery();
        $this->getQuerySortParams();
        return $this->getAllQueriedTeams();
    }

    /**
     * @return void
     */
    private function initializeTeamsQuery(): void
    {
        $this->teamsQuery = Team::query();
    }

    /**
     * @return void
     */
    private function getQuerySortParams(): void
    {
        if (isset($this->request->__order_by)) {
            $params = $this->getSortParamsFromRequest();
            $this->teamsQuery->orderBy($params['field'], $params['direction']);
        }
    }

    /**
     * @return mixed[]
     */
    private function getAllQueriedTeams(): array
    {
        return $this->teamsQuery->get()->toArray();
    }
}
