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
        Schema::create('replication_innovations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paper_id')->constrained('papers')->onDelete('cascade');
            $table->foreignId('person_in_charge')->constrained('users')->onDelete('cascade');
            $table->string('replication_status')->nullable();
            $table->string('event_news')->nullable();
            $table->string('evidence')->nullable();
            $table->string('financial_benefit')->nullable();
            $table->string('reward')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('replication_innovations');
    }
};