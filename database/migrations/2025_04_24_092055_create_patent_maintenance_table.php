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
        Schema::create('patent_maintenance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patent_id')->constrained('patent')->onDelete('cascade');
            $table->date('payment_date')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('payment_proof')->nullable();
            $table->enum('status', ['paid', 'unpaid'])->default('unpaid');
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
        Schema::dropIfExists('patent_maintenance');
    }
};