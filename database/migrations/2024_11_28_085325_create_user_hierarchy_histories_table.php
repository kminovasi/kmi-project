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
        Schema::create('user_hierarchy_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Relasi ke user
            $table->string('directorate_name')->nullable();
            $table->string('group_function_name')->nullable();
            $table->string('department_name')->nullable();
            $table->string('unit_name')->nullable();
            $table->string('section_name')->nullable();
            $table->string('sub_section_of')->nullable();
            $table->date('effective_start_date');
            $table->date('effective_end_date')->nullable(); // Null jika masih aktif
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_hierarchy_histories');
    }
};
