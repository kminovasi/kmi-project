<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::table('pvt_custom_benefits', function (Blueprint $table) {
            // Mengubah kolom value menjadi tipe teks
            $table->text('value')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Mengembalikan kolom value menjadi tipe integer
        DB::statement('ALTER TABLE pvt_custom_benefits ALTER COLUMN value TYPE INTEGER USING value::integer');
    }
};
