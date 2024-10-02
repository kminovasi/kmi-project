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
            if (!Schema::hasColumn('papers', 'inovasi_lokasi')) {
                $table->string('inovasi_lokasi')->nullable();
            }
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
            //menghapus kolom inovasi_lokasi
            $table->dropColumn('inovasi_lokasi');
        });
    }
};
