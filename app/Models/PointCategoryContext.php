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

    public function pointCategory() {
        return $this->belongsTo(PointCategory::class, "point_category_id");
    }

    public function subject() {
        return $this->belongsTo(Subject::class, "subject_id");
    }

    public function course() {
        return $this->belongsTo(Grade::class, "grade_id");
    }
}
