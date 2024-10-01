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
        if (Schema::hasColumn('pvt_assesment_team_judges', 'stage')) {
            Schema::table('pvt_assesment_team_judges', function (Blueprint $table) {
                $table->dropColumn('stage');
            });
        }

        Schema::table('pvt_assesment_team_judges', function (Blueprint $table) {
            $table->enum('stage', ['on desk', 'presentation', 'caucus'])->default('on desk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pvt_assesment_team_judges', function (Blueprint $table) {
            $table->dropIfExists('stage');
        });
    }
};
