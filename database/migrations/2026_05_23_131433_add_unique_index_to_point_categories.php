<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('point_categories', function (Blueprint $table) {
            $table->unique(['name', 'teacher_id', 'school_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('point_categories', function (Blueprint $table) {
            $table->dropUnique('point_categories_name_teacher_id_school_id_unique');
        });
    }
};
