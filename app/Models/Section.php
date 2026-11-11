<?php

namespace App\Models;

use App\Asignatura;
use App\Lapso;
use App\Lapso_Estudiante;
use App\Modules\Academic\Domain\GradeSheetStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Sección (persistencia y lectura). Reglas en App\Modules\Academic\Domain\Section.
 */
class Section extends Model
{
    protected $fillable = ['lapse_id', 'course_id', 'code', 'teacher_id', 'status', 'closed_at', 'closed_by', 'closed_forcibly'];

    protected $casts = [
        'status' => GradeSheetStatus::class,
        'closed_at' => 'datetime',
        'closed_forcibly' => 'boolean',
    ];

    public function lapse(): BelongsTo
    {
        return $this->belongsTo(Lapso::class, 'lapse_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Asignatura::class, 'course_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Lapso_Estudiante::class, 'section_id');
    }
}
