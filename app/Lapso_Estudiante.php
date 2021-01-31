<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lapso_Estudiante extends Model
{
    protected $table = 'lapses_students';

    protected $fillable = [
       'student_id','lapse_id','course_id','note','status'
    ];
}
