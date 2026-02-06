<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class School extends Model
{
    /** @use HasFactory<\Database\Factories\SchoolFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'user_id'
    ];

    protected $table = 'schools';

    protected function name() {
        return Attribute::make(
            get: fn (string $value) => Str::title($value),
            set: fn (string $value) => Str::title(trim($value)),
        );
    }

    public function scopeActive($query) {
        return $query->where('status', 'ACTIVE');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
