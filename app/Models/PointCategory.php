<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PointCategory extends Model
{
    /** @use HasFactory<\Database\Factories\PointCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        "name",
        "max_points",
        "teacher_id",
        "school_id",
        "status"
    ];

    public function scopeActive($query) {
        return $query->where("status", 'ACTIVE');
    }

    public function teacher() {
        return $this->belongsTo(Teacher::class);
    }

    public function pointCategoryContext() {
        return $this->hasMany(PointCategoryContext::class, "point_category_id");
    }
}
