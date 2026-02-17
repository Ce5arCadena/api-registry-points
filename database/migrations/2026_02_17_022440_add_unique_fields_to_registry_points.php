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
        Schema::table('registry_points', function (Blueprint $table) {
            $table->unique(['student_id', 'point_category_id', 'subject_id', 'school_id'], "registry_points_student_category_subject_school_unique");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registry_points', function (Blueprint $table) {
            $table->dropUnique("registry_points_student_category_subject_school_unique");
        });
    }
};
