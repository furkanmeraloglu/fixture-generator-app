<?php

namespace Tests\Unit;

use App\Models\Fixture;
use App\Models\FootballMatch;
use App\Models\Team;
use App\Services\Fixture\GenerateLeagueFixturesService;
use Tests\TestCase;

class GenerateLeagueFixturesServiceTest extends TestCase
{
    public function test_boot_function_creates_correct_match_pairs()
    {
        $team1 = Team::factory()->create();
        $team2 = Team::factory()->create();
        $team3 = Team::factory()->create();
        $team4 = Team::factory()->create();

        (new GenerateLeagueFixturesService())->boot();

        $matches = FootballMatch::with('homeTeam', 'awayTeam')->get();

        $matchCounts = [];

        foreach ($matches as $match) {
            $homeId = $match->home_team_id;
            $awayId = $match->away_team_id;

            $key = "$homeId-$awayId";
            $matchCounts[$key] = isset($matchCounts[$key]) ? $matchCounts[$key] + 1 : 1;
        }

        $expectedPairs = [
            "{$team1->team_id}-{$team2->team_id}",
            "{$team1->team_id}-{$team3->team_id}",
            "{$team1->team_id}-{$team4->team_id}",
            "{$team2->team_id}-{$team1->team_id}",
            "{$team2->team_id}-{$team3->team_id}",
            "{$team2->team_id}-{$team4->team_id}",
            "{$team3->team_id}-{$team1->team_id}",
            "{$team3->team_id}-{$team2->team_id}",
            "{$team3->team_id}-{$team4->team_id}",
            "{$team4->team_id}-{$team1->team_id}",
            "{$team4->team_id}-{$team2->team_id}",
            "{$team4->team_id}-{$team3->team_id}",
        ];

        foreach ($expectedPairs as $pair) {
            $this->assertEquals(1, $matchCounts[$pair] ?? 0, "Match pair $pair was not found or appeared more than once");
        }
    }

    public function test_boot_function_sets_correct_weeks_for_matches()
    {
        Team::factory()->count(4)->create();

        (new GenerateLeagueFixturesService())->boot();

        for ($week = 1; $week <= 6; $week++) {
            $matchesInWeek = FootballMatch::where('week', $week)->count();
            $this->assertEquals(2, $matchesInWeek, "Week $week should have exactly 2 matches");
        }

        for ($week = 1; $week <= 3; $week++) {
            $firstRoundMatches = FootballMatch::where('week', $week)->get();
            $secondRoundMatches = FootballMatch::where('week', $week + 3)->get();

            foreach ($firstRoundMatches as $index => $firstMatch) {
                $secondMatch = $secondRoundMatches[$index];
                $this->assertEquals($firstMatch->home_team_id, $secondMatch->away_team_id);
                $this->assertEquals($firstMatch->away_team_id, $secondMatch->home_team_id);
            }
        }
    }

    public function test_boot_function_throws_exception_for_odd_number_of_teams()
    {
        Team::factory()->count(5)->create();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The league fixtures can only be generated for even number of teams');

        (new GenerateLeagueFixturesService())->boot();
    }

    public function test_boot_function_sets_is_played_to_false_for_all_matches_and_fixtures()
    {
        Team::factory()->count(4)->create();

        (new GenerateLeagueFixturesService())->boot();

        $this->assertEquals(0, FootballMatch::where('is_played', true)->count());
        $this->assertEquals(12, FootballMatch::where('is_played', false)->count());

        $this->assertEquals(0, Fixture::where('is_played', true)->count());
        $this->assertEquals(12, Fixture::where('is_played', false)->count());
    }
}
