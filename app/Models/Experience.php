<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable('job_title', 'company', 'description', 'start_date', 'end_date', 'is_current')]
class Experience extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'start_date' => 'integer',
            'end_date' => 'integer',
            'is_current' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }
}
