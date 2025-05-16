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
            $table->text('description')->nullable()->after('reward');
            // Adding a new column 'description' to the 'replication_innovations' table
            // The column is of type text and can be null
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
            $table->dropColumn('description');
            // Dropping the 'description' column from the 'replication_innovations' table
        });
    }
};