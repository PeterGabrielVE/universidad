<?php

namespace App\Modules\Academic\Infrastructure;

use App\Modules\Academic\Application\Port\EnrollmentRepository;
use App\Modules\Academic\Application\Port\LapseRepository;
use App\Modules\Academic\Application\Port\ProgramRepository;
use Illuminate\Support\ServiceProvider;

final class AcademicServiceProvider extends ServiceProvider
{
    public array $bindings = [
        LapseRepository::class => EloquentLapseRepository::class,
        ProgramRepository::class => EloquentProgramRepository::class,
        EnrollmentRepository::class => EloquentEnrollmentRepository::class,
    ];
}
