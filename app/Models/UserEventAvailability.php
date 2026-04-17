<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserEventAvailability extends Model
{
    protected $table = 'user_event_availability';

    protected $fillable = [
        'user_id',
        'event_id',
        'available',
        'responded_at',
    ];

    protected $casts = [
        'available'    => 'boolean',
        'responded_at' => 'datetime',
    ];

    public function user()  { return $this->belongsTo(User::class); }
    public function event() { return $this->belongsTo(Event::class); }
}
