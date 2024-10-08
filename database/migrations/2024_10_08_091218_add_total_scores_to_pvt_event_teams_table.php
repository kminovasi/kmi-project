<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pvt_event_teams', function (Blueprint $table) {
            $table->decimal('total_score_on_desk', 8, 2)->nullable();
            $table->decimal('total_score_presentation', 8, 2)->nullable();
            $table->decimal('total_score_caucus', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pvt_event_teams', function (Blueprint $table) {
            $table->dropColumn('total_score_on_desk');
            $table->dropColumn('total_score_presentation');
            $table->dropColumn('total_score_caucus');
        });
    }
};
