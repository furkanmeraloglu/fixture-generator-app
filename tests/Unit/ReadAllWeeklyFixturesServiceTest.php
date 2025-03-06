<?php

namespace Tests\Unit;

use App\Models\Fixture;
use App\Models\FootballMatch;
use App\Models\Team;
use App\Services\Fixture\ReadAllWeeklyFixturesService;
use Illuminate\Http\Request;
use Tests\TestCase;

class ReadAllWeeklyFixturesServiceTest extends TestCase
{
    public function test_boot_function_returns_all_fixtures_with_matches_and_teams()
    {
        $homeTeam = Team::factory()->create([
            'name' => 'Barcelona',
            'strength' => 90,
            'points' => 20,
            'goals_scored' => 15,
            'goals_conceded' => 5,
        ]);

        $awayTeam = Team::factory()->create([
            'name' => 'Real Madrid',
            'strength' => 92,
            'points' => 18,
            'goals_scored' => 12,
            'goals_conceded' => 7,
        ]);


        $fixture1 = Fixture::factory()->create(['week' => 1, 'is_played' => true]);
        $fixture2 = Fixture::factory()->create(['week' => 2, 'is_played' => false]);


        FootballMatch::factory()->create([
            'fixture_id' => $fixture1->fixture_id,
            'week' => 1,
            'home_team_id' => $homeTeam->team_id,
            'away_team_id' => $awayTeam->team_id,
            'home_team_goals' => 3,
            'away_team_goals' => 1
        ]);

        FootballMatch::factory()->create([
            'fixture_id' => $fixture2->fixture_id,
            'week' => 2,
            'home_team_id' => $awayTeam->team_id,
            'away_team_id' => $homeTeam->team_id,
            'home_team_goals' => null,
            'away_team_goals' => null
        ]);

        $request = new Request();
        $response = (new ReadAllWeeklyFixturesService($request))->boot();

        $this->assertIsArray($response);
        $this->assertCount(2, $response);

        $this->assertEquals(1, $response[0]['week']);
        $this->assertEquals(2, $response[1]['week']);

        $this->assertCount(1, $response[0]['football_matches']);
        $this->assertEquals(3, $response[0]['football_matches'][0]['home_team_goals']);
        $this->assertEquals(1, $response[0]['football_matches'][0]['away_team_goals']);

        $this->assertEquals('Barcelona', $response[0]['football_matches'][0]['home_team']['name']);
        $this->assertEquals('Real Madrid', $response[0]['football_matches'][0]['away_team']['name']);

        $this->assertCount(1, $response[1]['football_matches']);
        $this->assertNull($response[1]['football_matches'][0]['home_team_goals']);
        $this->assertNull($response[1]['football_matches'][0]['away_team_goals']);
    }

    public function test_boot_function_returns_fixtures_ordered_by_week_asc()
    {
        Fixture::factory()->create(['week' => 3, 'is_played' => true]);
        Fixture::factory()->create(['week' => 1, 'is_played' => true]);
        Fixture::factory()->create(['week' => 2, 'is_played' => true]);

        $request = new Request(['__order_by' => 'week']);

        $response = (new ReadAllWeeklyFixturesService($request))->boot();

        $this->assertIsArray($response);
        $this->assertCount(3, $response);

        $this->assertEquals(1, $response[0]['week']);
        $this->assertEquals(2, $response[1]['week']);
        $this->assertEquals(3, $response[2]['week']);
    }

    public function test_boot_function_returns_fixtures_ordered_by_week_desc()
    {
        Fixture::factory()->create(['week' => 2]);
        Fixture::factory()->create(['week' => 3]);
        Fixture::factory()->create(['week' => 1]);

        $request = new Request(['__order_by' => '-week']);

        $response = (new ReadAllWeeklyFixturesService($request))->boot();

        $this->assertIsArray($response);
        $this->assertCount(3, $response);

        $this->assertEquals(3, $response[0]['week']);
        $this->assertEquals(2, $response[1]['week']);
        $this->assertEquals(1, $response[2]['week']);
    }

    public function test_boot_function_returns_empty_array_when_no_fixtures_exist()
    {
        $request = new Request();
        $response = (new ReadAllWeeklyFixturesService($request))->boot();

        $this->assertIsArray($response);
        $this->assertEmpty($response);
    }

    public function test_boot_function_returns_fixtures_with_no_matches()
    {
        Fixture::factory()->create(['week' => 1, 'is_played' => false]);

        $request = new Request();
        $response = (new ReadAllWeeklyFixturesService($request))->boot();

        $this->assertIsArray($response);
        $this->assertCount(1, $response);
        $this->assertEquals(1, $response[0]['week']);
        $this->assertArrayHasKey('football_matches', $response[0]);
        $this->assertEmpty($response[0]['football_matches']);
    }
}
