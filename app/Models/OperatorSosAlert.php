<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OperatorSosAlert extends Model {
    protected $fillable = ['assignment_id','event_id','lat','lng','message','resolved_by','resolved_at'];
    protected $casts = ['lat'=>'float','lng'=>'float','resolved_at'=>'datetime'];
    public function assignment() { return $this->belongsTo(EventAssignment::class); }
    public function resolver() { return $this->belongsTo(User::class, 'resolved_by'); }
    public function isActive(): bool { return $this->resolved_at === null; }
}
