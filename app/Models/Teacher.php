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
}
