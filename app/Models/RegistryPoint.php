<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistryPoint extends Model
{
    /** @use HasFactory<\Database\Factories\RegistryPointFactory> */
    use HasFactory;

    protected $fillable = [
        "points",
        "student_id",
        "point_category_id",
        "subject_id",
        "school_id",
        "created_at",
        "updated_at"
    ];
}
