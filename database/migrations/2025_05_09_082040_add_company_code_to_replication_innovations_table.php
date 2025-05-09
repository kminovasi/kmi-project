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
        Schema::table('replication_innovations', function (Blueprint $table) {
            $table->string('company_code')->after('person_in_charge')->nullable()->comment('Company code');
            $table->foreign('company_code')->references('company_code')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            // Add any other necessary columns or modifications here
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('replication_innovations', function (Blueprint $table) {
            $table->dropForeign(['company_code']);
            $table->dropColumn('company_code');
        });
    }
};