<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    // Auto-generate slug from name if not provided
    protected static function booted(): void
    {
        static::saving(function (Category $cat) {
            if (empty($cat->slug)) {
                $cat->slug = \Illuminate\Support\Str::slug($cat->name, '-', null) ?: uniqid('cat-');
            }
        });
    }
}
