<?php

namespace App\Modules\Academic\Infrastructure;

use App\Estudiante;
use App\Models\User;
use App\Modules\Academic\Application\Port\AcademicPeople;
use App\Modules\IdentityAccess\Domain\AccountStatus;
use App\Modules\IdentityAccess\Domain\RoleName;

final class EloquentAcademicPeople implements AcademicPeople
{
    public function isActiveTeacher(int $userId): bool
    {
        $user = User::find($userId);

        return $user !== null
            && $user->status === AccountStatus::Activo
            && $user->hasRole(RoleName::Docente->value);
    }

    public function programsOfStudent(int $studentId): ?array
    {
        $student = Estudiante::with('user.programs')->find($studentId);

        return $student === null ? null : ($student->user?->programs->pluck('id')->all() ?? []);
    }

    public function displayName(int $userId): string
    {
        $user = User::find($userId);

        return $user === null ? "Usuario {$userId}" : trim("{$user->name} {$user->last_name}");
    }
}
