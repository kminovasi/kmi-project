<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learn_share', function (Blueprint $table) {
            if (!Schema::hasColumn('learn_share', 'attachments')) {
                $table->json('attachments')->nullable()->after('participants');
            }

            if (!Schema::hasColumn('learn_share', 'status')) {
                $table->enum('status', ['Pending', 'Approved', 'Rejected'])
                      ->default('Pending')
                      ->after('attachments');
            }
        });
    }

    /**
     * Rollback kolom yang ditambahkan.
     */
    public function down(): void
    {
        Schema::table('learn_share', function (Blueprint $table) {
            if (Schema::hasColumn('learn_share', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('learn_share', 'attachments')) {
                $table->dropColumn('attachments');
            }
        });
    }
};
