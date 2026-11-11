<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Asignatura (tabla "courses"). Puede pertenecer al pensum de varios programas.
 */
class Asignatura extends Model
{
    protected $table = 'courses';

    protected $fillable = [
       'id', 'cod', 'name', 'uc', 'semester_id', 'required',
    ];

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Doctorado::class, 'program_course', 'course_id', 'program_id')->withTimestamps();
    }
}
