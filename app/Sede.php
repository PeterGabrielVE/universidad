<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    protected $table = 'headquarters';

    protected $fillable = [
       'id','name','created_at','description'
    ];
}

