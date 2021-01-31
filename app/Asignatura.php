<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asignatura extends Model
{
    protected $table = 'courses';

    protected $fillable = [
       'id','cod','name','uc','semester_id','required'
    ];

    public function asignatura()
    {
        return $this->belongsTo('App\Asignatura', 'course_id');
    }
}
