<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherSubject extends Model
{
    protected $table = "teacher_subject";

    protected $fillable = [
        "teacher_id",
        "subject_id",
        "grade_id",
        "school_id",
        "academic_year",
        "status",
    ];

    public function scopeActive($query) {
        return $query->where("status", 'ACTIVE');
    }

    public function teacher() {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function grade() {
        return $this->belongsTo(Grade::class);
    }
}
