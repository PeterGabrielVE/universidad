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
    /** Lapsos de cualquier programa. */
    case LapsesManage = 'lapses.manage';
    /** Lapsos/cohortes solo del programa propio (ProgramScope). */
    case LapsesManageProgram = 'lapses.manage_program';
    case ReportsView = 'reports.view';
    /** Facultades, programas y su pensum. */
    case ProgramsManage = 'programs.manage';
    /** Consultar programas y pensum (el Coordinador, solo los suyos). */
    case PensumView = 'pensum.view';
    case StudentsView = 'students.view';
    case StudentsManage = 'students.manage';
    case StudentsDelete = 'students.delete';
    case StudentsEnroll = 'students.enroll';
    case GradesView = 'grades.view';
    /** Registrar notas en el acta de las secciones propias (docente titular). */
    case GradesRecord = 'grades.record';
    /** Abrir secciones y asignar el docente titular (Coordinador: sus programas). */
    case SectionsManage = 'sections.manage';
    /** Corregir notas con el acta cerrada, con justificación y auditoría. */
    case GradesOverride = 'grades.override';
    /** Cierre administrativo forzoso de un acta. */
    case GradeSheetsForceClose = 'gradesheets.force_close';
    case DocumentsDownload = 'documents.download';

    /** Valor para el middleware "permission" de Spatie; varios permisos = cualquiera de ellos. */
    public static function middleware(self ...$permissions): string
    {
        return 'permission:' . implode('|', array_map(fn (self $p) => $p->value, $permissions));
    }
}
