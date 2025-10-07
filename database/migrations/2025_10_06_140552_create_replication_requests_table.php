<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('replication_requests', function (Blueprint $table) {
            $table->id();

            // Relasi ke tim & paper (optional kalau mau null-safe)
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('paper_id')->nullable();

            // Data inti
            $table->string('innovation_title'); 
            $table->string('pic_name');
            $table->string('pic_phone', 30);
            $table->string('unit_name')->nullable();
            $table->string('superior_name')->nullable();
            $table->string('plant_name')->nullable();
            $table->string('area_location')->nullable();
            $table->date('planned_date')->nullable();

            // Status proses
            $table->enum('status', ['pending','approved','rejected'])->default('pending');

            // Audit
            $table->unsignedBigInteger('created_by'); // user yang ajukan
            $table->timestamps();

            // FK & index
            $table->foreign('team_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->foreign('paper_id')->references('id')->on('papers')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();

            $table->index(['team_id','status']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('replication_requests');
    }
};
