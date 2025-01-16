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
        Schema::table('custom_benefit_financials', function (Blueprint $table) {
            // Menambahkan kolom is_deleted
            $table->boolean('is_deleted')->default(false)->after('name_benefit');

            // Menghapus kolom company_code
            if (Schema::hasColumn('custom_benefit_financials', 'company_code')) {
                $table->dropColumn('company_code');
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
        Schema::table('custom_benefit_financials', function (Blueprint $table) {
            // Menghapus kolom is_deleted
            $table->dropColumn('is_deleted');

            // Menambahkan kembali kolom company_code
            $table->string('company_code', 255)->nullable()->after('name_benefit');
        });
    }
};
