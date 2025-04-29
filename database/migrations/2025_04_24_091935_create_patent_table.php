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
        Schema::create('patent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paper_id')->constrained('papers')->onDelete('cascade');
            $table->string('registration_number')->nullable();
            $table->foreignId('person_in_charge')->constrained('users')->onDelete('cascade');
            $table->string('application_status')->nullable();
            $table->string('draft_paten')->nullable();
            $table->string('ownership_letter')->nullable();
            $table->string('statement_of_transfer_rights')->nullable();
            $table->string('certificate')->nullable();
            $table->string('certificate_number')->nullable();
            $table->string('paten_status')->nullable();
            $table->date('payment_deadline')->nullable();
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
        Schema::dropIfExists('patent');
    }
};