<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('learn_share', function (Blueprint $table) {
            $table->id();
            $table->string('title');                          
            $table->string('job_function')->nullable();       
            $table->string('competency')->nullable();         
            $table->string('requesting_department');          
            $table->dateTime('scheduled_at');                 
            $table->text('objective');                       
            $table->text('opening_speech')->nullable();       
            $table->json('speakers')->nullable();             
            $table->json('participants')->nullable();         

            $table->string('employee_id', 255)->nullable()->index();

            $table->timestamps();

            $table->index('scheduled_at');

            $table->foreign('employee_id')
                  ->references('employee_id')
                  ->on('users')
                  ->nullOnDelete(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learn_share');
    }
};
