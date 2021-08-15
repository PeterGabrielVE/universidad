<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post2 extends Model
{
    protected $table = 'posts_2';

    protected $fillable = [
       'extenso','carta_extenso','student_id','created_at','updated_at'
    ];
}
