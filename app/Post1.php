<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post1 extends Model
{
    protected $table = 'posts_1';

    protected $fillable = [
       'extenso','carta_extenso','student_id','created_at','updated_at'
    ];
}
