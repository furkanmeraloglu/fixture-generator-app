<?php

namespace Tests\Unit;

use App\Models\ChampionshipPrediction;
use App\Models\Fixture;
use App\Models\FootballMatch;
use App\Models\Team;
use App\Services\Fixture\ResetLeagueFixturesService;
use Illuminate\Http\Request;
use Tests\TestCase;

class ResetLeagueFixturesServiceTest extends TestCase
{
    public function test_boot_function_resets_all_data_and_generates_new_fixtures()
    {
        $teams = Team::factory()->count(4)->create([
            'points' => 10,
            'goals_scored' => 15,
            'goals_conceded' => 5,
            'wins' => 3,
            'losses' => 1,
            'draws' => 1,
            'played_matches' => 5,
        ]);

        $fixture1 = Fixture::factory()->create(['week' => 1, 'is_played' => true]);
        $fixture2 = Fixture::factory()->create(['week' => 2, 'is_played' => false]);

        FootballMatch::factory()->create([
            'fixture_id' => $fixture1->fixture_id,
            'week' => 1,
            'home_team_id' => $teams[0]->team_id,
            'away_team_id' => $teams[1]->team_id,
            'home_team_goals' => 2,
            'away_team_goals' => 1,
            'is_played' => true,
        ]);

        FootballMatch::factory()->create([
            'fixture_id' => $fixture1->fixture_id,
            'week' => 1,
            'home_team_id' => $teams[2]->team_id,
            'away_team_id' => $teams[3]->team_id,
            'home_team_goals' => 0,
            'away_team_goals' => 0,
            'is_played' => true,
        ]);

        FootballMatch::factory()->create([
            'fixture_id' => $fixture2->fixture_id,
            'week' => 2,
            'home_team_id' => $teams[0]->team_id,
            'away_team_id' => $teams[2]->team_id,
            'is_played' => false,
        ]);

        FootballMatch::factory()->create([
            'fixture_id' => $fixture2->fixture_id,
            'week' => 2,
            'home_team_id' => $teams[1]->team_id,
            'away_team_id' => $teams[3]->team_id,
            'is_played' => false,
        ]);

        ChampionshipPrediction::factory()->create([
            'week' => 1,
            'predictions' => 0.3
        ]);

        ChampionshipPrediction::factory()->create([
            'week' => 2,
            'predictions' => 0.2
        ]);

        $this->assertEquals(2, Fixture::count());
        $this->assertEquals(4, FootballMatch::count());
        $this->assertEquals(2, ChampionshipPrediction::count());
        $this->assertEquals(5, Team::first()->played_matches);

        $request = new Request();
        $response = (new ResetLeagueFixturesService($request))->boot();

        $this->assertEquals('All fixtures and related data have been reset successfully', $response['message']);

        $this->assertEquals(12, FootballMatch::count());
        $this->assertEquals(0, FootballMatch::where('is_played', true)->count());
        $this->assertEquals(12, Fixture::count());
        $this->assertEquals(0, Fixture::where('is_played', true)->count());
        $this->assertEquals(0, ChampionshipPrediction::count());
        foreach (Team::all() as $team) {
            $this->assertEquals(0, $team->points);
            $this->assertEquals(0, $team->goals_scored);
            $this->assertEquals(0, $team->goals_conceded);
            $this->assertEquals(0, $team->wins);
            $this->assertEquals(0, $team->losses);
            $this->assertEquals(0, $team->draws);
            $this->assertEquals(0, $team->played_matches);
        }
    }

    public function test_boot_function_rolls_back_transaction_on_error()
    {
        Team::factory()->count(3)->create();

        Fixture::factory()->create(['week' => 1, 'is_played' => true]);
        FootballMatch::factory()->create([
            'fixture_id' => Fixture::first()->fixture_id,
            'week' => 1,
            'home_team_id' => Team::first()->team_id,
            'away_team_id' => Team::skip(1)->first()->team_id,
        ]);

        $this->assertEquals(1, Fixture::count());
        $this->assertEquals(1, FootballMatch::count());

        $request = new Request();
        $service = new ResetLeagueFixturesService($request);

        try {
            $service->boot();
            $this->fail('An exception was expected but not thrown');
        } catch (\Exception $e) {
            $this->assertEquals('The fixture and matches could not be reset!', $e->getMessage());
            $this->assertEquals(1, Fixture::count());
            $this->assertEquals(1, FootballMatch::count());
        }
    }
}
