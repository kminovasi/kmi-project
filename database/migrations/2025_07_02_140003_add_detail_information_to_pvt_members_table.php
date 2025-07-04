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
        Schema::table('pvt_members', function (Blueprint $table) {
            $table->string('position_title')->nullable()->after('status');
            $table->string('directorate_name')->nullable()->after('position_title');
            $table->string('group_function_name')->nullable()->after('directorate_name');
            $table->string('department_name')->nullable()->after('group_function_name');
            $table->string('unit_name')->nullable()->after('department_name');
            $table->string('section_name')->nullable()->after('unit_name');
            $table->string('sub_section_of')->nullable()->after('section_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pvt_members', function (Blueprint $table) {
            $table->dropColumn([
                'position_title',
                'directorate_name',
                'group_function_name',
                'department_name',
                'unit_name',
                'section_name',
                'sub_section_of'
            ]);
        });
    }
};