<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('learn_share_speakers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('learn_share_id');

            $table->string('employee_id', 20);

            $table->timestamps();

            $table->foreign('learn_share_id')
                  ->references('id')->on('learn_share')
                  ->onDelete('cascade');

            $table->foreign('employee_id')
                  ->references('employee_id')->on('users')
                  ->onDelete('cascade');

            $table->unique(['learn_share_id', 'employee_id']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learn_share_speakers');
    }
};
