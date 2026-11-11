<?php

namespace App;

use App\Modules\Academic\Domain\EnrollmentResult;
use App\Modules\Academic\Domain\EnrollmentStatus;
use Illuminate\Database\Eloquent\Model;

/**
 * Inscripción de un estudiante en una asignatura de un lapso.
 */
class Lapso_Estudiante extends Model
{
    protected $table = 'lapses_students';

    protected $fillable = [
       'student_id', 'lapse_id', 'section_id', 'course_id', 'note', 'status',
    ];

    protected $casts = [
        'status' => EnrollmentStatus::class,
        'result' => EnrollmentResult::class,
    ];

    public function asignatura()
    {
        return $this->belongsTo('App\Asignatura', 'course_id');
    }

    public function section()
    {
        return $this->belongsTo(\App\Models\Section::class, 'section_id');
    }

    public function estudiante()
    {
        return $this->belongsTo(\App\Estudiante::class, 'student_id');
    }

    public function lapso()
    {
        return $this->belongsTo('App\Lapso', 'lapse_id');
    }
}
