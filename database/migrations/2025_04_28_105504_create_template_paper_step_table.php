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
        Schema::create('template_paper_step', function (Blueprint $table) {
            $table->id();
            $table->foreignId('metodologi_paper_id')->constrained('metodologi_papers')->onDelete('cascade');
            $table->string('step_1')->nullable();
            $table->string('step_2')->nullable();
            $table->string('step_3')->nullable();
            $table->string('step_4')->nullable();
            $table->string('step_5')->nullable();
            $table->string('step_6')->nullable();
            $table->string('step_7')->nullable();
            $table->string('step_8')->nullable();
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
        Schema::dropIfExists('template_paper_step');
    }
};