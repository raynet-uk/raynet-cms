<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OperatorWelfareResponse extends Model {
    public $timestamps = false;
    protected $fillable = ['welfare_check_id','assignment_id','responded','prompted_at','responded_at'];
    protected $casts = ['responded'=>'boolean','prompted_at'=>'datetime','responded_at'=>'datetime'];
}
