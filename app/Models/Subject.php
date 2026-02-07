<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    /** @use HasFactory<\Database\Factories\SubjectFactory> */
    use HasFactory;

    protected $fillable = [
        "name",
        "teacher_id",
        "grade_id",
        "school_id",
        "status",
    ];

    protected $table = "subjects";

    public function pointCategories() {
        return $this->hasMany(PointCategory::class);
    }
}
