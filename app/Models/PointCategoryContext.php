<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointCategoryContext extends Model
{
    protected $table = 'point_category_context';
    protected $fillable = [
        "point_category_id",
        "grade_id",
        "subject_id",
        "school_id",
        "status"
    ];

    public function scopeActive($query) {
        return $query->where("status", 'ACTIVE');
    }
}
