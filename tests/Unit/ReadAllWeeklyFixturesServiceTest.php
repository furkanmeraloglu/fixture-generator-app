<?php

namespace Tests\Unit;

use App\Models\Fixture;
use App\Models\FootballMatch;
use App\Models\Team;
use App\Services\Fixture\ReadAllWeeklyFixturesService;
use Tests\TestCase;

class ReadAllWeeklyFixturesServiceTest extends TestCase
{
    public function test_boot_function_returns_all_matches_grouped_by_week()
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

        $response = (new ReadAllWeeklyFixturesService())->boot();

        $this->assertIsArray($response);
        $this->assertArrayHasKey('1', $response);
        $this->assertArrayHasKey('2', $response);

        $this->assertCount(1, $response['1']);
        $this->assertEquals(3, $response['1'][0]['home_team_goals']);
        $this->assertEquals(1, $response['1'][0]['away_team_goals']);
        $this->assertEquals('Barcelona', $response['1'][0]['home_team']['name']);
        $this->assertEquals('Real Madrid', $response['1'][0]['away_team']['name']);

        $this->assertCount(1, $response['2']);
        $this->assertNull($response['2'][0]['home_team_goals']);
        $this->assertNull($response['2'][0]['away_team_goals']);
        $this->assertEquals('Real Madrid', $response['2'][0]['home_team']['name']);
        $this->assertEquals('Barcelona', $response['2'][0]['away_team']['name']);
    }

    public function test_boot_function_returns_empty_array_when_no_matches_exist()
    {
        $response = (new ReadAllWeeklyFixturesService())->boot();

        $this->assertIsArray($response);
        $this->assertEmpty($response);
    }

    public function test_matches_are_ordered_by_week_ascending()
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

        $response = (new ReadAllWeeklyFixturesService())->boot();

        $this->assertIsArray($response);
        $this->assertCount(2, $response);

        $keys = array_keys($response);
        $this->assertEquals(['1', '2'], $keys);
    }
}
