<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class NotATeacher extends DomainException
{
    public function __construct()
    {
        parent::__construct('El docente titular debe ser un usuario activo con rol Docente.');
    }

    public function field(): ?string
    {
        return 'teacher_id';
    }
}
