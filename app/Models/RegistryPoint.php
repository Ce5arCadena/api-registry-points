<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RegistryPoint extends Model
{
    /** @use HasFactory<\Database\Factories\RegistryPointFactory> */
    use HasFactory;

    protected $fillable = [
        "points",
        "student_id",
        "point_category_context_id",
        "teacher_id",
        "academic_year",
        "created_at",
        "updated_at"
    ];
}
