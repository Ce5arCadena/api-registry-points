<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

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
}
