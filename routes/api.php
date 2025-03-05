<?php

use App\Http\Controllers\FixtureController;
use App\Http\Controllers\FootballMatchController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;


Route::prefix('teams')->group(function () {
    Route::get('/', [TeamController::class, 'getAllTeams']);
    Route::get('/{team_id}', [TeamController::class, 'getTeam']);
});

Route::prefix('fixtures')->group(function () {
    Route::get('/', [FixtureController::class, 'getAllWeeklyFixtures']);
    Route::get('/{fixture_id}', [FixtureController::class, 'getWeeklyFixture']);
    Route::post('/', [FixtureController::class, 'generateLeagueFixtures']);
    Route::post('/simulate-week', [FixtureController::class, 'simulateWeek']);
    Route::post('/simulate-all', [FixtureController::class, 'simulateAllWeeks']);
    Route::delete('/', [FixtureController::class, 'resetAllFixturesAndFootballMatches']);
});
