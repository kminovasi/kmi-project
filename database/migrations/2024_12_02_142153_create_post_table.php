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
        Schema::create('post', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Judul
            $table->string('slug')->unique(); // Slug untuk URL
            $table->text('content'); // Isi berita
            $table->string('cover_image')->nullable(); // Gambar cover
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relasi ke user
            $table->unsignedInteger('views')->default(0); // Jumlah view
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
        Schema::dropIfExists('articles');
    }
};
