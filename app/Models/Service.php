<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable('title', 'description', 'icon', 'tags', 'sort_order')]
class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if ($service->sort_order === null) {
                $service->sort_order = self::max('sort_order') + 1;
            }
        });
    }

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'sort_order' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
