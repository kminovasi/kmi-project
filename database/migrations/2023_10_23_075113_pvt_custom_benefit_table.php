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
        Schema::create('pvt_custom_benefits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('custom_benefit_financial_id');
            $table->foreign('custom_benefit_financial_id')->references('id')->on('custom_benefit_financials')->onDelete('cascade');
            $table->unsignedBigInteger('paper_id');
            $table->foreign('paper_id')->references('id')->on('papers')->onDelete('cascade');
            $table->text('value')->nullable();
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
        Schema::dropIfExists('pvt_custom_benefit');
    }
};
