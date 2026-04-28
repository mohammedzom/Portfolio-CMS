<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['ip_address', 'user_agent', 'visited_at'])]
class Visit extends Model
{
    use HasFactory;

    protected $casts = [
        'visited_at' => 'date',
    ];
}
