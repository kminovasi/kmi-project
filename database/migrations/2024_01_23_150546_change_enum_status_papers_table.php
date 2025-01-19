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
        //
        Schema::table('papers', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('papers', function (Blueprint $table) {
            $table->enum('status', [
                'not finish',
                'upload full paper',
                'accepted paper by facilitator',
                'revision paper by facilitator',
                'rejected paper by facilitator',
                'upload benefit',
                'accepted benefit by facilitator',
                'revision benefit by facilitator',
                'rejected benefit by facilitator',
		        'accepted benefit by general manager',
		        'revision benefit by general manager',
                'rejected benefit by general manager',
                'accepted by innovation admin',
                'revision by innovation admin',
                'rejected by innovation admin',
                'replicate',
                'not complete',
                'rollback paper',
                'rollback benefit',
            ])->default('not finish');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('papers', function (Blueprint $table) {
            //
        });
    }
};
