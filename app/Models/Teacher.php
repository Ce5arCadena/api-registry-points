<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherFactory> */
    use HasFactory;

    protected $fillable = [
        "full_name",
        "document",
        "phone",
        "status",
        "school_id",
        "user_id",
    ];

    protected $table = 'teachers';

    public function scopeActive($query) {
        return $query->where('status', 'ACTIVE');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function subjectAssignments()
    {
        return $this->hasMany(TeacherSubject::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects');
    }
}
