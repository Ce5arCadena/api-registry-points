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
}
