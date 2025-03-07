<?php

namespace Tests\Unit;

use App\Exceptions\DataNotFoundException;
use App\Models\Fixture;
use App\Models\FootballMatch;
use App\Models\Team;
use App\Services\Fixture\ReadWeeklyFixtureService;
use Tests\TestCase;

class ReadWeeklyFixtureServiceTest extends TestCase
{
    public function test_boot_function_returns_fixture_data_with_matches_and_teams()
    {
        $homeTeam = Team::factory()->create([
            'name' => 'Barcelona',
            'strength' => 90,
            'points' => 20,
            'goals_scored' => 15,
            'goals_conceded' => 5,
            'wins' => 6,
            'losses' => 0,
            'draws' => 2,
            'played_matches' => 8
        ]);

        $awayTeam = Team::factory()->create([
            'name' => 'Real Madrid',
            'strength' => 92,
            'points' => 18,
            'goals_scored' => 12,
            'goals_conceded' => 7,
            'wins' => 5,
            'losses' => 0,
            'draws' => 3,
            'played_matches' => 8
        ]);

        $fixture = Fixture::factory()->create([
            'week' => 8,
            'is_played' => true,
        ]);

        FootballMatch::factory()->create([
            'fixture_id' => $fixture->fixture_id,
            'week' => 8,
            'home_team_id' => $homeTeam->team_id,
            'away_team_id' => $awayTeam->team_id,
            'home_team_goals' => 3,
            'away_team_goals' => 3,
            'is_played' => true
        ]);

        $response = (new ReadWeeklyFixtureService(8))->boot();

        $this->assertIsArray($response);
        $this->assertCount(1, $response);

        $this->assertEquals($fixture->fixture_id, $response[0]['fixture_id']);
        $this->assertEquals(8, $response[0]['week']);
        $this->assertEquals(3, $response[0]['home_team_goals']);
        $this->assertEquals(3, $response[0]['away_team_goals']);
        $this->assertTrue($response[0]['is_played']);

        $this->assertEquals($homeTeam->team_id, $response[0]['home_team']['team_id']);
        $this->assertEquals('Barcelona', $response[0]['home_team']['name']);
        $this->assertEquals(20, $response[0]['home_team']['points']);
        $this->assertEquals(15, $response[0]['home_team']['goals_scored']);
        $this->assertEquals(5, $response[0]['home_team']['goals_conceded']);
        $this->assertEquals(6, $response[0]['home_team']['wins']);
        $this->assertEquals(0, $response[0]['home_team']['losses']);
        $this->assertEquals(2, $response[0]['home_team']['draws']);
        $this->assertEquals(8, $response[0]['home_team']['played_matches']);

        $this->assertEquals($awayTeam->team_id, $response[0]['away_team']['team_id']);
        $this->assertEquals('Real Madrid', $response[0]['away_team']['name']);
        $this->assertEquals(18, $response[0]['away_team']['points']);
        $this->assertEquals(12, $response[0]['away_team']['goals_scored']);
        $this->assertEquals(7, $response[0]['away_team']['goals_conceded']);
        $this->assertEquals(5, $response[0]['away_team']['wins']);
        $this->assertEquals(0, $response[0]['away_team']['losses']);
        $this->assertEquals(3, $response[0]['away_team']['draws']);
        $this->assertEquals(8, $response[0]['away_team']['played_matches']);
    }

    public function test_boot_function_throws_exception_when_fixtures_not_found()
    {
        $this->expectException(DataNotFoundException::class);
        $this->expectExceptionMessage('Week fixture could not be found!');

        $service = new ReadWeeklyFixtureService(999);
        $service->boot();
    }

    public function test_boot_function_returns_fixture_data_with_multiple_matches()
    {
        $first_team = Team::factory()->create(['name' => 'Besiktas JK', 'strength' => 90]);
        $second_team = Team::factory()->create(['name' => 'Fenerbahce SK', 'strength' => 85]);
        $third_team = Team::factory()->create(['name' => 'Galatasaray SK', 'strength' => 80]);
        $fourth_team = Team::factory()->create(['name' => 'Trabzonspor', 'strength' => 75]);

        $fixture = Fixture::factory()->create(['week' => 8, 'is_played' => true]);

        FootballMatch::factory()->create([
            'fixture_id' => $fixture->fixture_id,
            'week' => 8,
            'home_team_id' => $first_team->team_id,
            'away_team_id' => $second_team->team_id,
            'home_team_goals' => 3,
            'away_team_goals' => 1
        ]);

        FootballMatch::factory()->create([
            'fixture_id' => $fixture->fixture_id,
            'week' => 8,
            'home_team_id' => $third_team->team_id,
            'away_team_id' => $fourth_team->team_id,
            'home_team_goals' => 1,
            'away_team_goals' => 4
        ]);

        $response = (new ReadWeeklyFixtureService(8))->boot();

        $this->assertIsArray($response);
        $this->assertCount(2, $response);

        $teamNames = [
            $response[0]['home_team']['name'] => true,
            $response[0]['away_team']['name'] => true,
            $response[1]['home_team']['name'] => true,
            $response[1]['away_team']['name'] => true,
        ];

        $this->assertArrayHasKey('Besiktas JK', $teamNames);
        $this->assertArrayHasKey('Fenerbahce SK', $teamNames);
        $this->assertArrayHasKey('Galatasaray SK', $teamNames);
        $this->assertArrayHasKey('Trabzonspor', $teamNames);
    }
}
