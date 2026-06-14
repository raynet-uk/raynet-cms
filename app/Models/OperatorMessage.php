<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OperatorMessage extends Model {
    protected $fillable = ['event_id','assignment_id','sent_by','type','body','payload','requires_ack'];
    protected $casts = ['payload'=>'array','requires_ack'=>'boolean'];
    public function acks() { return $this->hasMany(OperatorMessageAck::class, 'message_id'); }
    public function sender() { return $this->belongsTo(User::class, 'sent_by'); }
    public function assignment() { return $this->belongsTo(EventAssignment::class); }
}
