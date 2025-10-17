<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('procedures', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');           
            $table->string('title')->nullable();       
            $table->json('file_path')->nullable();    
            $table->timestamps();                     
            $table->index('employee_id');
            $table->index('title');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedures');
    }
};
