<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable('title', 'slug', 'description', 'category', 'tech_stack', 'images', 'live_url', 'repo_url', 'is_featured', 'sort_order')]
class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if ($project->sort_order === null) {
                $project->sort_order = self::max('sort_order') + 1;
            }
        });
    }

    protected function casts(): array
    {
        return [
            'tech_stack' => 'json',
            'images' => 'array',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
