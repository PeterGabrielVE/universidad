<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $table = 'students';

    protected $fillable = [
       'cedula','phone','lapse','country_id','user_id'
    ];
}
