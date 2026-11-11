<?php

namespace App\Modules\Academic\Infrastructure;

use App\Modules\Academic\Application\Dto\GradeCorrection;
use App\Modules\Academic\Application\Port\GradeAuditLog;
use Illuminate\Support\Facades\DB;

final class EloquentGradeAuditLog implements GradeAuditLog
{
    public function record(GradeCorrection $correction): void
    {
        DB::table('grade_audit_logs')->insert([
            'enrollment_id' => $correction->enrollmentId,
            'old_note' => $correction->oldGrade?->value,
            'new_note' => $correction->newGrade->value,
            'old_result' => $correction->oldResult?->value,
            'new_result' => $correction->newResult?->value,
            'justification' => $correction->justification->text,
            'changed_by' => $correction->changedBy,
            'changed_by_name' => $correction->changedByName,
            'created_at' => now(),
        ]);
    }
}
