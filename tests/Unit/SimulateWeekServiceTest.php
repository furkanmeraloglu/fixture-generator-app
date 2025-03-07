<?php

namespace Tests\Unit;

use App\Models\Fixture;
use App\Models\FootballMatch;
use App\Models\Team;
use App\Services\Fixture\SimulateWeekService;
use Exception;
use Tests\TestCase;

class SimulateWeekServiceTest extends TestCase
{
    public function test_boot_function_correctly_simulates_matches_for_a_week()
    {
        $homeTeam = Team::factory()->create([
            'name' => 'Home Team',
            'points' => 10,
            'goals_scored' => 15,
            'goals_conceded' => 5,
            'wins' => 3,
            'losses' => 1,
            'draws' => 1,
            'played_matches' => 5
        ]);
        $awayTeam = Team::factory()->create([
            'name' => 'Away Team',
            'points' => 8,
            'goals_scored' => 10,
            'goals_conceded' => 8,
            'wins' => 2,
            'losses' => 1,
            'draws' => 2,
            'played_matches' => 5
        ]);
        $fixture = Fixture::factory()->create([
            'week' => 6,
            'is_played' => false
        ]);
        FootballMatch::factory()->create([
            'fixture_id' => $fixture->fixture_id,
            'week' => 6,
            'home_team_id' => $homeTeam->team_id,
            'away_team_id' => $awayTeam->team_id,
            'is_played' => false,
            'home_team_goals' => null,
            'away_team_goals' => null
        ]);

        $service = new class extends SimulateWeekService {
            public function simulateFootballMatch($football_match, bool $is_prediction_required = true): array
            {
                $this->footballMatch = $football_match;
                $this->homeTeam = $football_match->homeTeam;
                $this->awayTeam = $football_match->awayTeam;

                return [
                    'home_score' => 2,
                    'away_score' => 1
                ];
            }
        };

        $result = $service->boot(6);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        $updatedMatch = FootballMatch::first();
        $this->assertEquals(2, $updatedMatch->home_team_goals);
        $this->assertEquals(1, $updatedMatch->away_team_goals);
        $this->assertTrue($updatedMatch->is_played);

        $updatedFixture = Fixture::first();
        $this->assertTrue($updatedFixture->is_played);

        $updatedHomeTeam = $homeTeam->fresh();
        $this->assertEquals(13, $updatedHomeTeam->points);
        $this->assertEquals(17, $updatedHomeTeam->goals_scored);
        $this->assertEquals(6, $updatedHomeTeam->goals_conceded);
        $this->assertEquals(4, $updatedHomeTeam->wins);
        $this->assertEquals(1, $updatedHomeTeam->losses);
        $this->assertEquals(1, $updatedHomeTeam->draws);
        $this->assertEquals(6, $updatedHomeTeam->played_matches);

        $updatedAwayTeam = $awayTeam->fresh();
        $this->assertEquals(8, $updatedAwayTeam->points);
        $this->assertEquals(11, $updatedAwayTeam->goals_scored);
        $this->assertEquals(10, $updatedAwayTeam->goals_conceded);
        $this->assertEquals(2, $updatedAwayTeam->wins);
        $this->assertEquals(2, $updatedAwayTeam->losses);
        $this->assertEquals(2, $updatedAwayTeam->draws);
        $this->assertEquals(6, $updatedAwayTeam->played_matches);
    }

    public function test_boot_function_correctly_handles_draw_results()
    {
        $homeTeam = Team::factory()->create([
            'points' => 10,
            'goals_scored' => 15,
            'goals_conceded' => 5,
            'wins' => 3,
            'losses' => 1,
            'draws' => 1,
            'played_matches' => 5
        ]);

        $awayTeam = Team::factory()->create([
            'points' => 8,
            'goals_scored' => 10,
            'goals_conceded' => 8,
            'wins' => 2,
            'losses' => 1,
            'draws' => 2,
            'played_matches' => 5
        ]);

        $fixture = Fixture::factory()->create([
            'week' => 6,
            'is_played' => false
        ]);

        FootballMatch::factory()->create([
            'fixture_id' => $fixture->fixture_id,
            'week' => 6,
            'home_team_id' => $homeTeam->team_id,
            'away_team_id' => $awayTeam->team_id,
            'is_played' => false
        ]);

        $service = new class extends SimulateWeekService {
            public function simulateFootballMatch($football_match, bool $is_prediction_required = true): array
            {
                $this->footballMatch = $football_match;
                $this->homeTeam = $football_match->homeTeam;
                $this->awayTeam = $football_match->awayTeam;

                return [
                    'home_score' => 1,
                    'away_score' => 1
                ];
            }
        };

        $result = $service->boot(6);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        $updatedHomeTeam = $homeTeam->fresh();
        $this->assertEquals(11, $updatedHomeTeam->points);
        $this->assertEquals(16, $updatedHomeTeam->goals_scored);
        $this->assertEquals(6, $updatedHomeTeam->goals_conceded);
        $this->assertEquals(3, $updatedHomeTeam->wins);
        $this->assertEquals(1, $updatedHomeTeam->losses);
        $this->assertEquals(2, $updatedHomeTeam->draws);
        $this->assertEquals(6, $updatedHomeTeam->played_matches);

        $updatedAwayTeam = $awayTeam->fresh();
        $this->assertEquals(9, $updatedAwayTeam->points);
        $this->assertEquals(11, $updatedAwayTeam->goals_scored);
        $this->assertEquals(9, $updatedAwayTeam->goals_conceded);
        $this->assertEquals(2, $updatedAwayTeam->wins);
        $this->assertEquals(1, $updatedAwayTeam->losses);
        $this->assertEquals(3, $updatedAwayTeam->draws);
        $this->assertEquals(6, $updatedAwayTeam->played_matches);
    }

    public function test_boot_function_correctly_handles_away_team_win()
    {
        $homeTeam = Team::factory()->create([
            'points' => 10,
            'goals_scored' => 15,
            'goals_conceded' => 5,
            'wins' => 3,
            'losses' => 1,
            'draws' => 1,
            'played_matches' => 5
        ]);

        $awayTeam = Team::factory()->create([
            'points' => 8,
            'goals_scored' => 10,
            'goals_conceded' => 8,
            'wins' => 2,
            'losses' => 1,
            'draws' => 2,
            'played_matches' => 5
        ]);

        $fixture = Fixture::factory()->create([
            'week' => 6,
            'is_played' => false
        ]);

        FootballMatch::factory()->create([
            'fixture_id' => $fixture->fixture_id,
            'week' => 6,
            'home_team_id' => $homeTeam->team_id,
            'away_team_id' => $awayTeam->team_id,
            'is_played' => false
        ]);

        $service = new class extends SimulateWeekService {
            public function simulateFootballMatch($football_match, bool $is_prediction_required = true): array
            {
                $this->footballMatch = $football_match;
                $this->homeTeam = $football_match->homeTeam;
                $this->awayTeam = $football_match->awayTeam;

                return [
                    'home_score' => 0,
                    'away_score' => 2
                ];
            }
        };

        $result = $service->boot(6);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        $updatedHomeTeam = $homeTeam->fresh();
        $this->assertEquals(10, $updatedHomeTeam->points);
        $this->assertEquals(15, $updatedHomeTeam->goals_scored);
        $this->assertEquals(7, $updatedHomeTeam->goals_conceded);
        $this->assertEquals(3, $updatedHomeTeam->wins);
        $this->assertEquals(2, $updatedHomeTeam->losses);
        $this->assertEquals(1, $updatedHomeTeam->draws);
        $this->assertEquals(6, $updatedHomeTeam->played_matches);

        $updatedAwayTeam = $awayTeam->fresh();
        $this->assertEquals(11, $updatedAwayTeam->points);
        $this->assertEquals(12, $updatedAwayTeam->goals_scored);
        $this->assertEquals(8, $updatedAwayTeam->goals_conceded);
        $this->assertEquals(3, $updatedAwayTeam->wins);
        $this->assertEquals(1, $updatedAwayTeam->losses);
        $this->assertEquals(2, $updatedAwayTeam->draws);
        $this->assertEquals(6, $updatedAwayTeam->played_matches);
    }

    public function test_boot_function_simulates_multiple_matches_for_a_week()
    {
        $team1 = Team::factory()->create(['name' => 'Team 1', 'strength' => 90]);
        $team2 = Team::factory()->create(['name' => 'Team 2', 'strength' => 80]);
        $team3 = Team::factory()->create(['name' => 'Team 3', 'strength' => 70]);
        $team4 = Team::factory()->create(['name' => 'Team 4', 'strength' => 60]);

        $fixture = Fixture::factory()->create([
            'week' => 1,
            'is_played' => false
        ]);

        FootballMatch::factory()->create([
            'fixture_id' => $fixture->fixture_id,
            'week' => 1,
            'home_team_id' => $team1->team_id,
            'away_team_id' => $team2->team_id,
            'is_played' => false
        ]);

        FootballMatch::factory()->create([
            'fixture_id' => $fixture->fixture_id,
            'week' => 1,
            'home_team_id' => $team3->team_id,
            'away_team_id' => $team4->team_id,
            'is_played' => false
        ]);

        $result = (new SimulateWeekService())->boot(1);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);

        $this->assertEquals(2, FootballMatch::where('is_played', true)->count());
        $this->assertEquals(0, FootballMatch::where('is_played', false)->count());
        $this->assertTrue($fixture->fresh()->is_played);
        $this->assertGreaterThan(0, $team1->fresh()->played_matches);
        $this->assertGreaterThan(0, $team2->fresh()->played_matches);
        $this->assertGreaterThan(0, $team3->fresh()->played_matches);
        $this->assertGreaterThan(0, $team4->fresh()->played_matches);
    }

    public function test_boot_function_throws_exception_when_all_fixtures_are_played()
    {
        Fixture::factory()->create([
            'week' => 1,
            'is_played' => true
        ]);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('All football matches of this season have been played. Please reset the season to simulate again.');
        (new SimulateWeekService())->boot(1);
    }
}
