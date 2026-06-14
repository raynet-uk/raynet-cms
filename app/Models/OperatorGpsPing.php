<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OperatorGpsPing extends Model {
    public $timestamps = false;
    protected $fillable = ['assignment_id','event_id','lat','lng','accuracy_m','heading','speed_ms','battery_pct','is_dead_reckoned','pinged_at'];
    protected $casts = ['lat'=>'float','lng'=>'float','is_dead_reckoned'=>'boolean','pinged_at'=>'datetime'];
    public function assignment() { return $this->belongsTo(EventAssignment::class); }
}
