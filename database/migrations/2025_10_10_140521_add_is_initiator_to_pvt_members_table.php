<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pvt_members', function (Blueprint $table) {
            $table->boolean('is_initiator')
                  ->default(0)
                  ->comment('1 = initiator, 0 = bukan')
                  ->after('status');
            $table->index('is_initiator', 'pvt_members_is_initiator_idx');
        });
    }

    public function down(): void
    {
        Schema::table('pvt_members', function (Blueprint $table) {
            $table->dropIndex('pvt_members_is_initiator_idx');
            $table->dropColumn('is_initiator');
        });
    }
};
