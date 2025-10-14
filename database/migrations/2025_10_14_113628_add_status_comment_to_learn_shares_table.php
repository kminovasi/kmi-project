<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('learn_share', function (Blueprint $table) {
            if (!Schema::hasColumn('learn_shares', 'status_comment')) {
                $table->text('status_comment')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('learn_shares', function (Blueprint $table) {
            if (Schema::hasColumn('learn_shares', 'status_comment')) {
                $table->dropColumn('status_comment');
            }
        });
    }
};
