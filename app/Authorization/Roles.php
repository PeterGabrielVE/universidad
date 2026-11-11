<?php

namespace App\Authorization;

/**
 * Nombres de los roles de Spatie y agrupaciones usadas por las rutas.
 *
 * Los grupos reproducen en el servidor lo que el sidebar ya mostraba a cada rol
 * (resources/views/layouts/sidebar.blade.php): gestión de usuarios, roles,
 * lapsos y reportes para Director/Administrador; listado de estudiantes y
 * calificación también para Coordinador/Asistente.
 */
final class Roles
{
    public const DIRECTOR = 'Director';
    public const COORDINADOR = 'Coordinador';
    public const ASISTENTE = 'Asistente';
    public const ESTUDIANTE = 'Estudiante';
    public const ADMINISTRADOR = 'Administrador';

    /** Gestión de usuarios, roles, lapsos y reportes. */
    public const ADMIN = [self::DIRECTOR, self::ADMINISTRADOR];

    /** Personal académico: todo lo anterior más estudiantes y calificaciones. */
    public const STAFF = [self::DIRECTOR, self::COORDINADOR, self::ASISTENTE, self::ADMINISTRADOR];

    /** Valor para el middleware "role" de Spatie, p. ej. "role:Director|Administrador". */
    public static function middleware(array $roles): string
    {
        return 'role:' . implode('|', $roles);
    }

    private function __construct()
    {
    }
}
