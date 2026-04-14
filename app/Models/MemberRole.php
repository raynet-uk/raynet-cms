<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberRole extends Model
{
    protected $table = 'member_roles';

    protected $fillable = [
        'name',
        'slug',
        'sort_order',
        'colour',
        'description',
        'is_active',
    ];
}
