<?php

namespace Tests\Unit;

use App\Models\Team;
use App\Services\Team\TeamReadAllService;
use Illuminate\Http\Request;
use Tests\TestCase;

class TeamReadAllServiceTest  extends TestCase
{
    public function test_boot_function_returns_all_teams_without_ordering()
    {
        $first_team = Team::factory()->create(['name' => 'Besiktas JK', 'strength' => 90]);
        $second_team = Team::factory()->create(['name' => 'Galatasaray SK', 'strength' => 80]);
        $third_team = Team::factory()->create(['name' => 'Fenerbahce SK', 'strength' => 85]);
        $fourth_team = Team::factory()->create(['name' => 'Trabzonspor', 'strength' => 75]);

        $request = new Request();

        $response = (new TeamReadAllService($request))->boot();

        $this->assertCount(4, $response);
        $this->assertContains($first_team->team_id, array_column($response, 'team_id'));
        $this->assertContains($second_team->team_id, array_column($response, 'team_id'));
        $this->assertContains($third_team->team_id, array_column($response, 'team_id'));
        $this->assertContains($fourth_team->team_id, array_column($response, 'team_id'));
    }

    public function test_boot_function_returns_all_teams_ordered_by_strength()
    {
        $first_team = Team::factory()->create(['name' => 'Besiktas JK', 'strength' => 90]);
        $second_team = Team::factory()->create(['name' => 'Galatasaray SK', 'strength' => 80]);
        $third_team = Team::factory()->create(['name' => 'Fenerbahce SK', 'strength' => 85]);
        $fourth_team = Team::factory()->create(['name' => 'Trabzonspor', 'strength' => 75]);

        $request = new Request(['__order_by' => '-strength']);

        $response = (new TeamReadAllService($request))->boot();

        $this->assertCount(4, $response);
        $this->assertEquals(90, $response[0]['strength']);
        $this->assertEquals(85, $response[1]['strength']);
        $this->assertEquals(80, $response[2]['strength']);
        $this->assertEquals(75, $response[3]['strength']);

        $request = new Request(['__order_by' => 'strength']);

        $response = (new TeamReadAllService($request))->boot();

        $this->assertCount(4, $response);
        $this->assertEquals(75, $response[0]['strength']);
        $this->assertEquals(80, $response[1]['strength']);
        $this->assertEquals(85, $response[2]['strength']);
        $this->assertEquals(90, $response[3]['strength']);
    }

    public function test_boot_function_returns_all_teams_ordered_by_name()
    {
        $first_team = Team::factory()->create(['name' => 'Besiktas JK', 'strength' => 90]);
        $second_team = Team::factory()->create(['name' => 'Galatasaray SK', 'strength' => 80]);
        $third_team = Team::factory()->create(['name' => 'Fenerbahce SK', 'strength' => 85]);
        $fourth_team = Team::factory()->create(['name' => 'Trabzonspor', 'strength' => 75]);

        $request = new Request(['__order_by' => '-name']);

        $response = (new TeamReadAllService($request))->boot();

        $this->assertCount(4, $response);
        $this->assertEquals('Trabzonspor', $response[0]['name']);
        $this->assertEquals('Galatasaray SK', $response[1]['name']);
        $this->assertEquals('Fenerbahce SK', $response[2]['name']);
        $this->assertEquals('Besiktas JK', $response[3]['name']);

        $request = new Request(['__order_by' => 'name']);

        $response = (new TeamReadAllService($request))->boot();

        $this->assertCount(4, $response);
        $this->assertEquals('Besiktas JK', $response[0]['name']);
        $this->assertEquals('Fenerbahce SK', $response[1]['name']);
        $this->assertEquals('Galatasaray SK', $response[2]['name']);
        $this->assertEquals('Trabzonspor', $response[3]['name']);
    }

    public function test_boot_function_returns_null_array_if_there_is_no_teams()
    {
        $request = new Request();

        $response = (new TeamReadAllService($request))->boot();

        $this->assertIsArray($response);
        $this->assertEmpty($response);
    }
}
