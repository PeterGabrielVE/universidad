<?php

namespace App\Modules\Academic\Infrastructure;

use App\Modules\Academic\Application\Port\AcademicPeople;
use App\Modules\Academic\Application\Port\EnrollmentRepository;
use App\Modules\Academic\Application\Port\GradeAuditLog;
use App\Modules\Academic\Application\Port\SectionRepository;
use App\Modules\Academic\Application\Port\FacultyRepository;
use App\Modules\Academic\Application\Port\PensumRepository;
use App\Modules\Academic\Application\Port\LapseRepository;
use App\Modules\Academic\Application\Port\ProgramRepository;
use Illuminate\Support\ServiceProvider;

final class AcademicServiceProvider extends ServiceProvider
{
    public array $bindings = [
        LapseRepository::class => EloquentLapseRepository::class,
        ProgramRepository::class => EloquentProgramRepository::class,
        EnrollmentRepository::class => EloquentEnrollmentRepository::class,
        FacultyRepository::class => EloquentFacultyRepository::class,
        PensumRepository::class => EloquentPensumRepository::class,
        SectionRepository::class => EloquentSectionRepository::class,
        GradeAuditLog::class => EloquentGradeAuditLog::class,
        AcademicPeople::class => EloquentAcademicPeople::class,
    ];
}
