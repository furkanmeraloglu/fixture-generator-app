<?php

namespace App\Services\Fixture;

use App\Models\Fixture;
use App\Traits\ReadServicesTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ReadAllWeeklyFixturesService
{
    use ReadServicesTrait;

    protected Request $request;
    protected Builder $fixturesQuery;
    protected Fixture $serviceModelInstance;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->serviceModelInstance = new Fixture();
    }

    /**
     * @return array
     */
    public function boot(): array
    {
        $this->initializeFixturesQuery();
        $this->getQuerySortParams();
        return $this->getAllQueriedFixtures();
    }

    /**
     * @return void
     */
    private function initializeFixturesQuery(): void
    {
        $this->fixturesQuery = Fixture::query();
    }

    /**
     * @return void
     */
    private function getQuerySortParams(): void
    {
        if (isset($this->request->__order_by)) {
            $params = $this->getSortParamsFromRequest();
            $this->fixturesQuery->orderBy($params['field'], $params['direction']);
        }
    }

    /**
     * @return array
     */
    private function getAllQueriedFixtures(): array
    {
        return $this->fixturesQuery->with(
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
            ])->get()
            ->toArray();
    }
}
