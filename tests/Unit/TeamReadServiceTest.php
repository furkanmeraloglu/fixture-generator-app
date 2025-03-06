<?php

namespace Tests\Unit;

use App\Exceptions\DataNotFoundException;
use App\Models\Team;
use App\Services\Team\TeamReadService;
use Tests\TestCase;

class TeamReadServiceTest extends TestCase
{
    public function test_boot_function_returns_team_data_when_team_exists()
    {
        $team = Team::factory()->create([
            'name' => 'Besiktas JK',
            'strength' => 90
        ]);
        $response = (new TeamReadService($team->team_id))->boot();

        $this->assertIsArray($response);
        $this->assertEquals($team->team_id, $response['team_id']);
        $this->assertEquals('Besiktas JK', $response['name']);
        $this->assertEquals(90, $response['strength']);
        $this->assertArrayHasKey('team_all_matches', $response);
    }

    public function test_boot_function_throws_exception_when_team_does_not_exist()
    {
        $nonExistentTeamId = 9999;

        $this->expectException(DataNotFoundException::class);
        $this->expectExceptionMessage('Team could not be found!');

        (new TeamReadService($nonExistentTeamId))->boot();
    }
}
