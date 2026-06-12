<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grade extends Model
{
    /** @use HasFactory<\Database\Factories\GradeFactory> */
    use HasFactory;

    protected $fillable = [
        "name",
        "school_id",
        "status"
    ];

    protected $table = 'grades';
    
    public function scopeActive($query) {
        return $query->where('status', 'ACTIVE');
    }

    public function students() {
        return $this->hasMany(Student::class);
    }

    public function teacherAssignments()
    {
        return $this->hasMany(TeacherSubject::class, 'teacher_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subjects');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject')
            ->where('academic_year', now()->year);
    }
}
