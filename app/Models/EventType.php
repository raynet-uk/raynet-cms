<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sort_order',
        'colour',   // ✅ allow mass assignment of colour
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}