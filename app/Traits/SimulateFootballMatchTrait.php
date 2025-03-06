<?php

namespace App\Traits;

use App\Models\FootballMatch;
use App\Models\Team;

trait SimulateFootballMatchTrait
{
    protected float $homeAdvantage = 1.1;
    protected float $calculatedHomeAttackPower;
    protected float $calculatedAwayAttackPower;
    protected int $homeTeamGoalkeeperFactor;
    protected int $awayTeamGoalkeeperFactor;
    protected int $homeTeamCumulativePerformance;
    protected int $awayTeamCumulativePerformance;
    protected int $homeAttackPositionCounts;
    protected int $awayAttackPositionCounts;
    protected ?FootballMatch $footballMatch;
    protected ?Team $homeTeam;
    protected ?Team $awayTeam;

    /**
     * @param $football_match
     * @return array
     */
    public function simulateFootballMatch($football_match): array
    {
        $this->resetClassVariables();
        $this->week = $football_match->week;
        $this->setHomeAndAwayTeamsOfTheCurrentFootballMatch($football_match);
        $this->checkIfChampionshipPredictionIsRequired();
        $this->getTeamsCumulativePerformanceScore();
        $this->calculateEachTeamAttackPower();
        $this->setGoalKeeperFactorForEachTeamOfTheCurrentFootballMatch();
        $this->setPositionCountsForEachTeamOfTheCurrentFootballMatch();
        return $this->simulateTheCurrentFootballMatch();
    }

    /**
     * @return void
     */
    private function resetClassVariables(): void
    {
        $this->week = null;
        $this->footballMatch = null;
        $this->homeTeam = null;
        $this->awayTeam = null;
        $this->calculatedHomeAttackPower = 0.0;
        $this->calculatedAwayAttackPower = 0.0;
        $this->homeTeamGoalkeeperFactor = 0;
        $this->awayTeamGoalkeeperFactor = 0;
        $this->homeTeamCumulativePerformance = 0;
        $this->awayTeamCumulativePerformance = 0;
        $this->homeAttackPositionCounts = 0;
        $this->awayAttackPositionCounts = 0;
        $this->isChampionshipPredictionRequired = false;
    }

    /**
     * @param FootballMatch $football_match
     * @return void
     */
    private function setHomeAndAwayTeamsOfTheCurrentFootballMatch(FootballMatch $football_match): void
    {
        $this->footballMatch = $football_match;
        $this->homeTeam = $football_match->homeTeam;
        $this->awayTeam = $football_match->awayTeam;
    }

    /**
     * @return void
     */
    private function checkIfChampionshipPredictionIsRequired(): void
    {
        if ($this->footballMatch->week >= 4 && $this->footballMatch->week != 6) {
            $this->isChampionshipPredictionRequired = true;
        }
    }

    /**
     * @return void
     */
    private function getTeamsCumulativePerformanceScore(): void
    {
        $this->homeTeamCumulativePerformance = round($this->homeTeam->goals_scored - $this->homeTeam->goals_conceded);
        $this->awayTeamCumulativePerformance = round($this->awayTeam->goals_scored - $this->awayTeam->goals_conceded);
    }

    /**
     * @return void
     */
    private function calculateEachTeamAttackPower(): void
    {
        $this->calculatedHomeAttackPower = $this->homeTeam->strength;
        $this->calculatedAwayAttackPower = $this->awayTeam->strength;
    }

    /**
     * @return void
     */
    private function setGoalKeeperFactorForEachTeamOfTheCurrentFootballMatch(): void
    {
        // Goalkeeper factor is a random number between 0 and 2.
        // The random number represents:
        // 0 => bad performance
        // 1 => normal performance
        // 2 => good performance.
        $this->homeTeamGoalkeeperFactor = mt_rand(0, 2);
        $this->awayTeamGoalkeeperFactor = mt_rand(0, 2);
    }

    /**
     * @return void
     */
    private function setPositionCountsForEachTeamOfTheCurrentFootballMatch(): void
    {
        $home_overall_strength_for_the_match = round($this->calculatedHomeAttackPower * $this->homeAdvantage - ($this->awayTeamGoalkeeperFactor + $this->awayTeamCumulativePerformance));
        $away_overall_strength_for_the_match = round($this->calculatedAwayAttackPower - ($this->homeTeamGoalkeeperFactor + $this->homeTeamCumulativePerformance));
        if ($home_overall_strength_for_the_match > $away_overall_strength_for_the_match) {
            $this->homeAttackPositionCounts = mt_rand(1, 5);
            $this->awayAttackPositionCounts = mt_rand(0, 5);
        } else {
            $this->awayAttackPositionCounts = mt_rand(1, 5);
            $this->homeAttackPositionCounts = mt_rand(0, 5);
        }

    }

    /**
     * @return int[]
     */
    private function simulateTheCurrentFootballMatch(): array
    {
        $home_score = 0;
        $away_score = 0;
        for ($i = 0; $i < $this->homeAttackPositionCounts; $i++) {
            $home_score += mt_rand(0, 1);
        }

        for ($i = 0; $i < $this->homeAttackPositionCounts; $i++) {
            $away_score += mt_rand(0, 1);
        }

        return [
            'home_score' => $home_score,
            'away_score' => $away_score
        ];
    }

}
