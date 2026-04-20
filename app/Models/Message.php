<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable('name', 'email', 'subject', 'body')]
class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }
}
