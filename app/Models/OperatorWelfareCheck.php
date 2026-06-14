<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OperatorWelfareCheck extends Model {
    protected $fillable = ['event_id','created_by','interval_minutes','active','last_sent_at'];
    protected $casts = ['active'=>'boolean','last_sent_at'=>'datetime'];
    public function responses() { return $this->hasMany(OperatorWelfareResponse::class, 'welfare_check_id'); }
}
