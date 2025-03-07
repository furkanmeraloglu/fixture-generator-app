<?php

use App\Http\Controllers\ChampionshipPredictionController;
use App\Http\Controllers\FixtureController;
use App\Http\Controllers\FootballMatchController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;


Route::prefix('teams')->group(function () {
    Route::get('/', [TeamController::class, 'getAllTeams']);
    Route::get('/{team_id}', [TeamController::class, 'getTeam']);
});

Route::get('/predictions/{week}', [ChampionshipPredictionController::class, 'getWeekPredictions']);

Route::patch('/matches/{fixture_id}', [FootballMatchController::class, 'updateMatchResult']);

Route::prefix('fixtures')->group(function () {
    Route::get('/', [FixtureController::class, 'getAllWeeklyFixtures']);
    Route::get('/{fixture_id}', [FixtureController::class, 'getWeeklyFixture']);
    Route::post('/', [FixtureController::class, 'generateLeagueFixtures']);
    Route::post('/simulate-week', [FixtureController::class, 'simulateWeekMatches']);
    Route::post('/simulate-all', [FixtureController::class, 'simulateAllWeeksMatches']);
    Route::delete('/', [FixtureController::class, 'resetAllFixturesAndFootballMatchesAndReGenerateNewFixtures']);
});
