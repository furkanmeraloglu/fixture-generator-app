<?php

namespace Tests\Unit;

use App\Models\ChampionshipPrediction;
use App\Models\Fixture;
use App\Models\FootballMatch;
use App\Models\Team;
use App\Services\FootballMatch\UpdateMatchResultService;
use App\Traits\GenerateChampionshipPercentageTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class UpdateMatchResultServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_boot_function_successfully_updates_match_result()
    {
        $homeTeam = Team::factory()->create([
            'points' => 3,
            'goals_scored' => 3,
            'goals_conceded' => 1,
            'wins' => 1,
            'losses' => 0,
            'draws' => 0,
            'played_matches' => 1
        ]);

        $awayTeam = Team::factory()->create([
            'points' => 0,
            'goals_scored' => 1,
            'goals_conceded' => 3,
            'wins' => 0,
            'losses' => 1,
            'draws' => 0,
            'played_matches' => 1
        ]);

        $fixture = Fixture::factory()->create(['week' => 3, 'is_played' => true]);

        $footballMatch = FootballMatch::factory()->create([
            'fixture_id' => $fixture->fixture_id,
            'week' => 3,
            'home_team_id' => $homeTeam->team_id,
            'away_team_id' => $awayTeam->team_id,
            'home_team_goals' => 3,
            'away_team_goals' => 1,
            'is_played' => true
        ]);

        $request = new Request([
            'home_team_goals' => 2,
            'away_team_goals' => 2
        ]);

        $service = new UpdateMatchResultService($request, $fixture->fixture_id);
        $result = $service->boot();

        $this->assertEquals(['message' => 'The football match has been updated successfully!'], $result);

        $updatedMatch = FootballMatch::find($footballMatch->football_match_id);
        $this->assertEquals(2, $updatedMatch->home_team_goals);
        $this->assertEquals(2, $updatedMatch->away_team_goals);

        $updatedHomeTeam = Team::find($homeTeam->team_id);
        $updatedAwayTeam = Team::find($awayTeam->team_id);

        $this->assertEquals(1, $updatedHomeTeam->points);
        $this->assertEquals(2, $updatedHomeTeam->goals_scored);
        $this->assertEquals(2, $updatedHomeTeam->goals_conceded);
        $this->assertEquals(0, $updatedHomeTeam->wins);
        $this->assertEquals(0, $updatedHomeTeam->losses);
        $this->assertEquals(1, $updatedHomeTeam->draws);

        $this->assertEquals(1, $updatedAwayTeam->points);
        $this->assertEquals(2, $updatedAwayTeam->goals_scored);
        $this->assertEquals(2, $updatedAwayTeam->goals_conceded);
        $this->assertEquals(0, $updatedAwayTeam->wins);
        $this->assertEquals(0, $updatedAwayTeam->losses);
        $this->assertEquals(1, $updatedAwayTeam->draws);
    }

    public function test_boot_function_throws_exception_for_unplayed_match()
    {
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $fixture = Fixture::factory()->create(['week' => 1, 'is_played' => false]);

        FootballMatch::factory()->create([
            'fixture_id' => $fixture->fixture_id,
            'home_team_id' => $homeTeam->team_id,
            'away_team_id' => $awayTeam->team_id,
            'week' => 1,
            'is_played' => false
        ]);

        $request = new Request([
            'home_team_goals' => 2,
            'away_team_goals' => 1
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The football match could not be updated!');

        $service = new UpdateMatchResultService($request, $fixture->fixture_id);
        $service->boot();
    }

    public function test_boot_function_updates_championship_prediction_when_week_is_appropriate()
    {
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $fixture = Fixture::factory()->create(['week' => 4, 'is_played' => true]);

        FootballMatch::factory()->create([
            'fixture_id' => $fixture->fixture_id,
            'week' => 4,
            'home_team_id' => $homeTeam->team_id,
            'away_team_id' => $awayTeam->team_id,
            'home_team_goals' => 1,
            'away_team_goals' => 0,
            'is_played' => true
        ]);

        ChampionshipPrediction::create([
            'week' => 4,
            'predictions' => []
        ]);

        $mockService = new class(new Request(['home_team_goals' => 2, 'away_team_goals' => 1]), $fixture->fixture_id) extends UpdateMatchResultService {
            public function generateChampionshipPredictionIfRequired(): array
            {
                return [
                    ['team' => 'Team A', 'championship_percentage' => 50],
                    ['team' => 'Team B', 'championship_percentage' => 30]
                ];
            }
        };

        $mockService->boot();

        $prediction = ChampionshipPrediction::where('week', 4)->first();
        $this->assertNotNull($prediction);
        $this->assertEquals('Team A', $prediction->predictions['0']['team']);
        $this->assertEquals(50, $prediction->predictions['0']['championship_percentage']);
        $this->assertEquals('Team B', $prediction->predictions['1']['team']);
        $this->assertEquals(30, $prediction->predictions['1']['championship_percentage']);
    }

    public function test_boot_function_does_not_update_championship_prediction_for_week_six()
    {
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $fixture = Fixture::factory()->create(['week' => 6, 'is_played' => true]);

        FootballMatch::factory()->create([
            'fixture_id' => $fixture->fixture_id,
            'week' => 6,
            'home_team_id' => $homeTeam->team_id,
            'away_team_id' => $awayTeam->team_id,
            'home_team_goals' => 1,
            'away_team_goals' => 0,
            'is_played' => true
        ]);

        $mockService = $this->getMockBuilder(UpdateMatchResultService::class)
            ->setConstructorArgs([new Request(['home_team_goals' => 2, 'away_team_goals' => 1]), $fixture->fixture_id])
            ->onlyMethods(['generateChampionshipPredictionIfRequired'])
            ->getMock();

        $mockService->expects($this->never())
            ->method('generateChampionshipPredictionIfRequired');

        $mockService->boot();
    }

    public function test_boot_function_correctly_handles_win_to_loss_transition()
    {
        // Create teams with initial stats
        $homeTeam = Team::factory()->create([
            'points' => 3,
            'goals_scored' => 3,
            'goals_conceded' => 1,
            'wins' => 1,
            'losses' => 0,
            'draws' => 0,
            'played_matches' => 1
        ]);

        $awayTeam = Team::factory()->create([
            'points' => 0,
            'goals_scored' => 1,
            'goals_conceded' => 3,
            'wins' => 0,
            'losses' => 1,
            'draws' => 0,
            'played_matches' => 1
        ]);

        // Create fixture and match
        $fixture = Fixture::factory()->create(['week' => 3, 'is_played' => true]);

        FootballMatch::factory()->create([
            'fixture_id' => $fixture->fixture_id,
            'week' => 3,
            'home_team_id' => $homeTeam->team_id,
            'away_team_id' => $awayTeam->team_id,
            'home_team_goals' => 3,
            'away_team_goals' => 1,
            'is_played' => true
        ]);

        // Change a win to a loss
        $request = new Request([
            'home_team_goals' => 0,
            'away_team_goals' => 2
        ]);

        $service = new UpdateMatchResultService($request, $fixture->fixture_id);
        $service->boot();

        // Check team stats after update
        $updatedHomeTeam = Team::find($homeTeam->team_id);
        $updatedAwayTeam = Team::find($awayTeam->team_id);

        // Home team should have 0 points (lost 3 points)
        $this->assertEquals(0, $updatedHomeTeam->points);
        $this->assertEquals(0, $updatedHomeTeam->goals_scored);
        $this->assertEquals(2, $updatedHomeTeam->goals_conceded);
        $this->assertEquals(0, $updatedHomeTeam->wins);
        $this->assertEquals(1, $updatedHomeTeam->losses);
        $this->assertEquals(0, $updatedHomeTeam->draws);

        // Away team should have 3 points (gained 3 points)
        $this->assertEquals(3, $updatedAwayTeam->points);
        $this->assertEquals(2, $updatedAwayTeam->goals_scored);
        $this->assertEquals(0, $updatedAwayTeam->goals_conceded);
        $this->assertEquals(1, $updatedAwayTeam->wins);
        $this->assertEquals(0, $updatedAwayTeam->losses);
        $this->assertEquals(0, $updatedAwayTeam->draws);
    }
}
