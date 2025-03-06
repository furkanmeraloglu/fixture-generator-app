<?php

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
        Schema::table('championship_predictions', function (Blueprint $table) {
            $table->dropColumn('team_id');
            $table->dropColumn('probability_percentage');
            $table->jsonb('predictions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('championship_predictions', function (Blueprint $table) {
            $table->foreignIdFor(Team::class, 'team_id')->constrained('teams', 'team_id')->onDelete('cascade');
            $table->decimal('probability_percentage', 5, 2)->unsigned();
            $table->dropColumn('predictions');
        });
    }
};
