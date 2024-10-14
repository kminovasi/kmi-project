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
            // Menambahkan kolom is_best_of_the_best
            $table->boolean('is_best_of_the_best')->default(false)->after('final_score');
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
            // Menghapus kolom is_best_of_the_best
            $table->dropColumn('is_best_of_the_best');
        });
    }
};
