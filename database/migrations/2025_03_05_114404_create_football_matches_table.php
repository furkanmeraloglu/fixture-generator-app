<?php

use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('football_matches', function (Blueprint $table) {
            $table->id('football_match_id');
            $table->foreignIdFor(Team::class, 'home_team_id')->constrained('teams', 'team_id')->onDelete('cascade');
            $table->foreignIdFor(Team::class, 'away_team_id')->constrained('teams', 'team_id')->onDelete('cascade');
            $table->integer('home_team_goals')->unsigned();
            $table->integer('away_team_goals')->unsigned();
            $table->foreignIdFor(Fixture::class, 'fixture_id')->constrained('fixtures', 'fixture_id')->onDelete('cascade');
            $table->integer('week')->unsigned();
            $table->boolean('is_played')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('football_matches');
    }
};
