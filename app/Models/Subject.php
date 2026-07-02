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
        "school_id",
        "status",
    ];

    protected $table = "subjects";

    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    public function teacherAssignments()
    {
        return $this->hasMany(TeacherSubject::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subjects');
    }

    public function grades()
    {
        return $this->belongsToMany(Grade::class, 'teacher_subjects', 'subject_id', 'grade_id')->distinct();
    }

    public function pointCategoryContexts()
    {
        return $this->hasMany(PointCategoryContext::class, 'subject_id');
    }
}
