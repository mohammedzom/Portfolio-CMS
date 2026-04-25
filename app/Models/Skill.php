<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable('skill_category_id', 'name', 'icon', 'proficiency')]
class Skill extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'proficiency' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    public function getLevelAttribute()
    {
        return match (true) {
            $this->proficiency >= 95 => 'Expert',
            $this->proficiency >= 85 => 'Advanced',
            $this->proficiency >= 75 => 'Upper Intermediate',
            $this->proficiency >= 60 => 'Intermediate',
            $this->proficiency >= 50 => 'Lower Intermediate',
            $this->proficiency >= 40 => 'Beginner',
            default => 'Novice',
        };
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SkillCategory::class, 'skill_category_id');
    }
}
