<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // I want to be explicit about what can be mass-assigned
    protected $fillable = [
        'name',
        'slug',
        'sort_order',
        'colour',
        'description',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];
}