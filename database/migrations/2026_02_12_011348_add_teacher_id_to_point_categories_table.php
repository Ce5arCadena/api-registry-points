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
            $table->foreignId("teacher_id")->after("max_points")->constrained('teachers')->onDelete('restrict')->nullable();
            $table->dropUnique(['name', 'subject_id', 'school_id']);
            $table->unique(['name', 'subject_id', 'teacher_id', 'school_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('point_categories', function (Blueprint $table) {
            $table->dropUnique(['name', 'subject_id', 'teacher_id', 'school_id']);
            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
            $table->unique(['name', 'subject_id', 'school_id']);
        });
    }
};
