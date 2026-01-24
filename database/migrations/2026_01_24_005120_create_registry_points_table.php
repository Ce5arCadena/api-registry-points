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
        Schema::create('registry_points', function (Blueprint $table) {
            $table->id();
            $table->integer('points');
            $table->foreignId('student_id')->constrained('students')->onDelete('restrict');
            $table->foreignId('point_category_id')->constrained('point_categories')->onDelete('restrict');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('restrict');
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registry_points');
    }
};
