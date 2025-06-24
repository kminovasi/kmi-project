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
        Schema::table('patent', function (Blueprint $table) {
            $table->string('administrative_file')->nullable()->after('statement_of_transfer_rights');
            $table->string('publication_file')->nullable()->after('administrative_file');
            $table->string('appeal_file')->nullable()->after('publication_file');
            $table->string('reject_file')->nullable()->after('appeal_file');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patent', function (Blueprint $table) {
            $table->dropColumn('administrative_file');
            $table->dropColumn('publication_file');
            $table->dropColumn('appeal_file');
            $table->dropColumn('reject_file');
        });
    }
};