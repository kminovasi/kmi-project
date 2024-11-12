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
        Schema::table('pvt_assesment_team_judges', function (Blueprint $table) {
            $table->enum('stage', ['on desk', 'presentation'])->default('on desk');
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
            //
        });
    }
};
