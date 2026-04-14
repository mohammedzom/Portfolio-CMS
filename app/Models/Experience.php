<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable('job_title', 'company', 'years', 'description', 'start_date', 'end_date')]
class Experience extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'start_date' => 'integer',
            'end_date' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }
}
