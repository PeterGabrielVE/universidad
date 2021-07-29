<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Doctorado extends Model
{
    protected $table = 'doctorados';

    protected $fillable = [
       'id','name','created_at','description'
    ];
}
