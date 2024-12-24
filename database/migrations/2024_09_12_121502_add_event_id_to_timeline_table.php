<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('timeline', function (Blueprint $table) {
            $table->unsignedBigInteger('event_id')->after('id'); // Add new column
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade'); // Add foreign key constraint
        });
    }

    public function down()
    {
        Schema::table('timeline', function (Blueprint $table) {
            $table->dropForeign(['event_id']); // Drop foreign key constraint
            $table->dropColumn('event_id'); // Drop column
        });
    }
};
