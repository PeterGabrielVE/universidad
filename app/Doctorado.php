<?php

namespace App;

use App\Models\Faculty;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Programa académico (tabla histórica "doctorados"). Pertenece a una facultad,
 * define su nota mínima aprobatoria y su pensum (FUNCTIONAL_SPECIFICATIONS.md §3, §13, §15).
 * Las reglas de alta, edición y baja están en ManagePrograms.
 */
class Doctorado extends Model
{
    use HasFactory;

    protected $table = 'doctorados';

    protected $fillable = [
       'id', 'faculty_id', 'name', 'description', 'min_passing_grade', 'created_at',
    ];

    protected $casts = [
        'min_passing_grade' => 'float',
    ];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /** Pensum: asignaturas del programa (N:M). */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Asignatura::class, 'program_course', 'program_id', 'course_id')->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'program_user', 'program_id', 'user_id')->withTimestamps();
    }

    public function lapses(): HasMany
    {
        return $this->hasMany(Lapso::class, 'program_id');
    }
}
