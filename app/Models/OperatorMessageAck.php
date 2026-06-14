<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OperatorMessageAck extends Model {
    public $timestamps = false;
    protected $fillable = ['message_id','assignment_id','acked_at'];
    protected $casts = ['acked_at'=>'datetime'];
}
