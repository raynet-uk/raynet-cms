<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsScormData extends Model
{
    protected $table = 'lms_scorm_data';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'key',
        'value',
    ];
}
