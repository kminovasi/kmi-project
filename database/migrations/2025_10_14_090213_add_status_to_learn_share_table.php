<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('learn_share', function (Blueprint $table) {
            $table->string('status', 20)->default('Pending')->after('opening_speech');
            $table->index('status');
        });
    }
    public function down(): void {
        Schema::table('learn_share', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn('status');
        });
    }
};