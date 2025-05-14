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
        Schema::create('coaching_clinics', function (Blueprint $table) {
            $table->id();
            // Tambahkan kolom sebelum foreign key
            $table->string('person_in_charge');
            $table->string('company_code');
            $table->unsignedBigInteger('team_id');

            $table->date('coaching_date')->nullable();
            $table->string('evidence')->nullable();
            $table->integer('coaching_duration')->nullable();
            $table->enum('status', ['accept', 'pending', 'reject', 'finish'])->default('pending');
            $table->timestamps();

            // Baru kemudian tambahkan foreign key
            $table->foreign('person_in_charge')->references('employee_id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('company_code')->references('company_code')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade')->onUpdate('cascade');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coaching_clinics');
    }
};