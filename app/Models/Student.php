<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;

    protected $fillable = [
        "name",
        "last_name",
        "document",
        "phone",
        "grade_id",
        "school_id",
        "status"
    ];

    public function scopeActive($query) {
        return $query->where("status", 'ACTIVE');
    }

    public function grade() {
        return $this->belongsTo(Grade::class);
    }

    public function registryPoints() {
        return $this->hasMany(RegistryPoint::class);
    }
}
