<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('replication_requests', function (Blueprint $table) {
            // pakai BIGINT unsigned
            $table->unsignedBigInteger('financial_benefit')
                  ->nullable()
                  ->after('planned_date');

            $table->unsignedBigInteger('potential_benefit')
                  ->nullable()
                  ->after('financial_benefit');

            // ENUM: replicated | no
            $table->enum('replication_status', ['replicated', 'no'])
                  ->nullable()
                  ->default('no')
                  ->after('potential_benefit');

            // simpan banyak file dalam JSON array
            $table->json('files')
                  ->nullable()
                  ->after('replication_status');
        });
    }

    public function down(): void {
        Schema::table('replication_requests', function (Blueprint $table) {
            $table->dropColumn([
                'financial_benefit',
                'potential_benefit',
                'replication_status',
                'files',
            ]);
        });
    }
};