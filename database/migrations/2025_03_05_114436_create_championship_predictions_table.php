<?php

use App\Models\Team;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('championship_predictions', function (Blueprint $table) {
            $table->id('championship_prediction_id');
            $table->foreignIdFor(Team::class, 'team_id')->constrained('teams', 'team_id')->onDelete('cascade');
            $table->integer('week')->unsigned();
            $table->decimal('probability_percentage', 5, 2)->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('championship_predictions');
    }
};
