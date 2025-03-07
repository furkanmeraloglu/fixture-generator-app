<?php

namespace Tests\Unit;

use App\Models\Fixture;
use App\Models\FootballMatch;
use App\Models\Team;
use App\Services\Fixture\SimulateAllWeeksService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimulateAllWeeksServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_boot_function_simulates_all_unplayed_matches_in_correct_order()
    {
        $team1 = Team::factory()->create([
            'name' => 'Team 1',
            'points' => 0,
            'goals_scored' => 0,
            'goals_conceded' => 0,
            'wins' => 0,
            'losses' => 0,
            'draws' => 0,
            'played_matches' => 0
        ]);
        $team2 = Team::factory()->create([
            'name' => 'Team 2',
            'points' => 0,
            'goals_scored' => 0,
            'goals_conceded' => 0,
            'wins' => 0,
            'losses' => 0,
            'draws' => 0,
            'played_matches' => 0
        ]);
        $fixture1 = Fixture::factory()->create([
            'fixture_id' => 1,
            'week' => 1,
            'is_played' => false
        ]);
        $fixture2 = Fixture::factory()->create([
            'fixture_id' => 2,
            'week' => 2,
            'is_played' => false
        ]);

        FootballMatch::factory()->create([
            'fixture_id' => $fixture1->fixture_id,
            'week' => 1,
            'home_team_id' => $team1->team_id,
            'away_team_id' => $team2->team_id,
            'is_played' => false
        ]);
        FootballMatch::factory()->create([
            'fixture_id' => $fixture2->fixture_id,
            'week' => 2,
            'home_team_id' => $team2->team_id,
            'away_team_id' => $team1->team_id,
            'is_played' => false
        ]);

        $result = (new class extends SimulateAllWeeksService {
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
        })->boot();

        $this->assertEquals(2, FootballMatch::where('is_played', true)->count());
        $this->assertEquals(0, FootballMatch::where('is_played', false)->count());

        $this->assertTrue($fixture1->fresh()->is_played);
        $this->assertTrue($fixture2->fresh()->is_played);

        $updatedTeam1 = $team1->fresh();
        $updatedTeam2 = $team2->fresh();

        $this->assertEquals(3, $updatedTeam1->points);
        $this->assertEquals(3, $updatedTeam1->goals_scored);
        $this->assertEquals(3, $updatedTeam1->goals_conceded);
        $this->assertEquals(1, $updatedTeam1->wins);
        $this->assertEquals(1, $updatedTeam1->losses);
        $this->assertEquals(0, $updatedTeam1->draws);
        $this->assertEquals(2, $updatedTeam1->played_matches);

        $this->assertEquals(3, $updatedTeam2->points);
        $this->assertEquals(3, $updatedTeam2->goals_scored);
        $this->assertEquals(3, $updatedTeam2->goals_conceded);
        $this->assertEquals(1, $updatedTeam2->wins);
        $this->assertEquals(1, $updatedTeam2->losses);
        $this->assertEquals(0, $updatedTeam2->draws);
        $this->assertEquals(2, $updatedTeam2->played_matches);
    }

    public function test_boot_function_handles_multiple_teams_and_weeks()
    {
        $teams = [];
        for ($i = 1; $i <= 4; $i++) {
            $teams[$i] = Team::factory()->create([
                'name' => "Team $i",
                'points' => 0,
                'goals_scored' => 0,
                'goals_conceded' => 0,
                'wins' => 0,
                'losses' => 0,
                'draws' => 0,
                'played_matches' => 0
            ]);
        }

        $fixtures = [];
        for ($week = 1; $week <= 3; $week++) {
            $fixtures[$week] = Fixture::factory()->create([
                'fixture_id' => $week,
                'week' => $week,
                'is_played' => false
            ]);
        }

        FootballMatch::factory()->create([
            'fixture_id' => $fixtures[1]->fixture_id,
            'week' => 1,
            'home_team_id' => $teams[1]->team_id,
            'away_team_id' => $teams[2]->team_id,
            'is_played' => false
        ]);
        FootballMatch::factory()->create([
            'fixture_id' => $fixtures[1]->fixture_id,
            'week' => 1,
            'home_team_id' => $teams[3]->team_id,
            'away_team_id' => $teams[4]->team_id,
            'is_played' => false
        ]);

        FootballMatch::factory()->create([
            'fixture_id' => $fixtures[2]->fixture_id,
            'week' => 2,
            'home_team_id' => $teams[1]->team_id,
            'away_team_id' => $teams[3]->team_id,
            'is_played' => false
        ]);
        FootballMatch::factory()->create([
            'fixture_id' => $fixtures[2]->fixture_id,
            'week' => 2,
            'home_team_id' => $teams[2]->team_id,
            'away_team_id' => $teams[4]->team_id,
            'is_played' => false
        ]);

        FootballMatch::factory()->create([
            'fixture_id' => $fixtures[3]->fixture_id,
            'week' => 3,
            'home_team_id' => $teams[1]->team_id,
            'away_team_id' => $teams[4]->team_id,
            'is_played' => false
        ]);
        FootballMatch::factory()->create([
            'fixture_id' => $fixtures[3]->fixture_id,
            'week' => 3,
            'home_team_id' => $teams[2]->team_id,
            'away_team_id' => $teams[3]->team_id,
            'is_played' => false
        ]);

        $service = new SimulateAllWeeksService();
        $result = $service->boot();

        $this->assertEquals(6, FootballMatch::where('is_played', true)->count());
        $this->assertEquals(0, FootballMatch::where('is_played', false)->count());

        foreach ($fixtures as $fixture) {
            $this->assertTrue($fixture->fresh()->is_played);
        }

        foreach ($teams as $team) {
            $this->assertEquals(3, $team->fresh()->played_matches);
        }
    }

    public function test_boot_function_throws_exception_when_all_matches_are_played()
    {
        Fixture::factory()->create([
            'week' => 1,
            'is_played' => true
        ]);
        Team::factory()->create();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('All football matches of this season have been played. Please reset the season to simulate again.');

        (new SimulateAllWeeksService())->boot();
    }

    public function test_boot_function_handles_draws_correctly()
    {
        $team1 = Team::factory()->create([
            'points' => 0,
            'goals_scored' => 0,
            'goals_conceded' => 0,
            'wins' => 0,
            'losses' => 0,
            'draws' => 0,
            'played_matches' => 0
        ]);
        $team2 = Team::factory()->create([
            'points' => 0,
            'goals_scored' => 0,
            'goals_conceded' => 0,
            'wins' => 0,
            'losses' => 0,
            'draws' => 0,
            'played_matches' => 0
        ]);

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

        (new class extends SimulateAllWeeksService {
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
        })->boot();

        $updatedTeam1 = $team1->fresh();
        $updatedTeam2 = $team2->fresh();

        $this->assertEquals(1, $updatedTeam1->points);
        $this->assertEquals(1, $updatedTeam1->goals_scored);
        $this->assertEquals(1, $updatedTeam1->goals_conceded);
        $this->assertEquals(0, $updatedTeam1->wins);
        $this->assertEquals(0, $updatedTeam1->losses);
        $this->assertEquals(1, $updatedTeam1->draws);
        $this->assertEquals(1, $updatedTeam1->played_matches);

        $this->assertEquals(1, $updatedTeam2->points);
        $this->assertEquals(1, $updatedTeam2->goals_scored);
        $this->assertEquals(1, $updatedTeam2->goals_conceded);
        $this->assertEquals(0, $updatedTeam2->wins);
        $this->assertEquals(0, $updatedTeam2->losses);
        $this->assertEquals(1, $updatedTeam2->draws);
        $this->assertEquals(1, $updatedTeam2->played_matches);
    }
}
