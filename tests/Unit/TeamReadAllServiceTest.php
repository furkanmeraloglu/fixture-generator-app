<?php

namespace Tests\Unit;

use App\Models\Team;
use App\Services\Team\TeamReadAllService;
use Tests\TestCase;

class TeamReadAllServiceTest extends TestCase
{
    public function test_boot_function_returns_all_teams_ordered_by_points_and_score_average()
    {
        $first_team = Team::factory()->create([
            'name' => 'Besiktas JK',
            'points' => 30,
            'goals_scored' => 20,
            'goals_conceded' => 10
        ]);
        $second_team = Team::factory()->create([
            'name' => 'Galatasaray SK',
            'points' => 30,
            'goals_scored' => 25,
            'goals_conceded' => 15
        ]);
        $third_team = Team::factory()->create([
            'name' => 'Fenerbahce SK',
            'points' => 25,
            'goals_scored' => 18,
            'goals_conceded' => 12
        ]);
        $fourth_team = Team::factory()->create([
            'name' => 'Trabzonspor',
            'points' => 20,
            'goals_scored' => 15,
            'goals_conceded' => 15
        ]);

        $response = (new TeamReadAllService())->boot();

        $this->assertCount(4, $response);

        $this->assertEquals(30, $response[0]['points']);
        $this->assertEquals(30, $response[1]['points']);
        $this->assertEquals(25, $response[2]['points']);
        $this->assertEquals(20, $response[3]['points']);

        // Same points, same average scores. Besiktas is leaper because of the name.
        $this->assertEquals('Besiktas JK', $response[0]['name']);
        $this->assertEquals('Galatasaray SK', $response[1]['name']);
    }

    public function test_boot_function_returns_empty_array_if_there_are_no_teams()
    {
        $response = (new TeamReadAllService())->boot();

        $this->assertIsArray($response);
        $this->assertEmpty($response);
    }
}
