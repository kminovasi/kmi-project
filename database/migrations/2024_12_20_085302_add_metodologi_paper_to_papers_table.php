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
        Schema::table('papers', function (Blueprint $table) {
            $table->unsignedBigInteger('metodologi_paper_id')->nullable()->after('team_id');
            $table->foreign('metodologi_paper_id')->references('id')->on('metodologi_papers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('papers', function (Blueprint $table) {
            $table->dropForeign(['metodologi_paper_id']);
            $table->dropColumn('metodologi_paper_id');
        });
    }
};
