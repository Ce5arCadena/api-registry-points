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
            // Solo student_id tiene foreign key real
            $table->dropForeign(['student_id']);

            // Las otras solo tienen índices, no foreign keys
            $table->dropIndex('registry_points_point_category_id_foreign');
            $table->dropIndex('registry_points_subject_id_foreign');
            $table->dropIndex('registry_points_school_id_foreign');

            // Ahora sí eliminar el índice único
            $table->dropUnique('registry_points_student_category_subject_school_unique');

            // Eliminar columnas
            $table->dropColumn(['point_category_id', 'subject_id', 'school_id']);

            // Agregar nuevas columnas
            $table->foreignId('point_category_context_id')
                ->after('student_id')
                ->constrained('point_category_context')
                ->cascadeOnDelete();

            $table->foreignId('teacher_id')
                ->after('point_category_context_id')
                ->constrained('teachers')
                ->cascadeOnDelete();

            $table->year('academic_year')
                ->after('points')
                ->default(now()->year);

            // Nuevo índice único
            $table->unique(['student_id', 'point_category_context_id', 'academic_year'], 'registry_points_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registry_points', function (Blueprint $table) {
            $table->dropUnique('registry_points_unique');
            $table->dropForeign(['point_category_context_id']);
            $table->dropForeign(['teacher_id']);
            $table->dropColumn(['point_category_context_id', 'teacher_id', 'academic_year']);

            $table->unsignedBigInteger('point_category_id')->after('student_id');
            $table->unsignedBigInteger('subject_id')->after('point_category_id');
            $table->unsignedBigInteger('school_id')->after('subject_id');

            $table->index('point_category_id');
            $table->index('subject_id');
            $table->index('school_id');
            $table->foreignId('student_id')->change()->constrained('students')->cascadeOnDelete();

            $table->unique(
                ['student_id', 'point_category_id', 'subject_id', 'school_id'],
                'registry_points_student_category_subject_school_unique'
            );
        });
    }
};
