<?php

namespace App\Services\Team;

use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TeamReadAllService
{
    protected Request $request;
    protected Builder $teamsQuery;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
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
            if (is_string($this->request->__order_by) && str_contains($this->request->__order_by, ',')) {
                $sortParams = explode(',', $this->request->__order_by);
            } else if (is_array($this->request->__order_by)) {
                $sortParams = $this->request->__order_by;
            } else {
                $sortParams = [$this->request->__order_by];
            }

            foreach ($sortParams as $orderBy) {
                if (in_array($orderBy, Schema::getColumnListing((new Team)->getTable()))) {
                    $field = str_replace('-', '', $orderBy);
                    $direction = str_starts_with($orderBy, '-') ? 'DESC' : 'ASC';
                    $this->teamsQuery->orderBy($field, $direction);
                }
            }
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
