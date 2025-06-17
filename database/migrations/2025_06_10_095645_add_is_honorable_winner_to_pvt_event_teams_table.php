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
            $table->boolean('is_honorable_winner')->default(false)->after('is_best_of_the_best');
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
            $table->dropColumn('is_honorable_winner');
        });
    }
};