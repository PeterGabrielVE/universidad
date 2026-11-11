<?php

namespace App\Modules\IdentityAccess\Domain;

/**
 * Permisos de las funcionalidades que existen hoy. Los de módulos aún no
 * construidos (pagos, jurados, actas) se añaden con su módulo.
 */
enum Permission: string
{
    case DashboardView = 'dashboard.view';
    case UsersView = 'users.view';
    case UsersManage = 'users.manage';
    case RolesManage = 'roles.manage';
    case LapsesManage = 'lapses.manage';
    case ReportsView = 'reports.view';
    case StudentsView = 'students.view';
    case StudentsManage = 'students.manage';
    case StudentsDelete = 'students.delete';
    case StudentsEnroll = 'students.enroll';
    case GradesView = 'grades.view';
    case GradesRecord = 'grades.record';
    case DocumentsDownload = 'documents.download';

    /** Valor para el middleware "permission" de Spatie; varios permisos = cualquiera de ellos. */
    public static function middleware(self ...$permissions): string
    {
        return 'permission:' . implode('|', array_map(fn (self $p) => $p->value, $permissions));
    }
}
