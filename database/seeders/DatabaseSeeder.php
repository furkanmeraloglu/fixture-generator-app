<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $teams = [
            [
                'name' => 'Manchester City F.C.',
                'strength' => 95,
            ],
            [
                'name' => 'Arsenal F.C.',
                'strength' => 88,
            ],
            [
                'name' => 'Chelsea F.C.',
                'strength' => 85,
            ],
            [
                'name' => 'Liverpool F.C.',
                'strength' => 95,
            ],
        ];
        Team::query()->insert($teams);
    }
}
