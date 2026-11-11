# Plan de implementación

Evolución incremental, sin reescritura. Al cerrar cada fase: pruebas en verde,
PROJECT_STATUS.md actualizado y riesgos revisados.

| Fase | Objetivo | Estado |
|---|---|---|
| 0 | Cerrar los hallazgos críticos de seguridad (C1–C8) | ✅ Completada |
| 1 | Base de pruebas: factories de clase, pruebas de caracterización de lo que funciona | ✅ Completada |
| 2 | IdentityAccess: unificar `User`, roles solo con Spatie, roles Docente/Jurado y Aspirante, matriz RBAC (§7), estado de cuenta, CRUD de usuarios y permisos | Siguiente |
| 3 | Academic: facultades/programas con `min_passing_grade`, cierre de lapso correcto, inscripción, notas, kardex, pensum | Pendiente |
| 4 | DoctoralSubmissions: tablas de entregas (migraciones aditivas), carga, reemplazo, evaluación numérica y nota ponderada | Pendiente |
| 5 | Reporting: constancias, récord académico, actas y PDFs corregidos | Pendiente |
| 6 | Modernización de dependencias con justificación individual | Pendiente |
| 7 | Documentación final: README, ARCHITECTURE y revisión | Pendiente |

Admisiones y Finanzas (FUNCTIONAL_SPECIFICATIONS.md §1) se planificarán cuando
tengan requisitos detallados: no se implementan sobre suposiciones.

## Fase 1: base de pruebas

1. Factories de clase (`Database\Factories`) en lugar de la sintaxis legacy de
   `UserFactory`, que no funciona en Laravel 10.
2. Pruebas de caracterización de lo que hoy funciona: login, panel, listado de
   estudiantes, CRUD de lapsos y roles, filtros de usuarios.
3. Criterio de salida: cada funcionalidad marcada como "Implementada" en
   PROJECT_STATUS.md tiene al menos una prueba.

## Fase 2: IdentityAccess (arquitectura objetivo)

```
app/Modules/IdentityAccess/
├── Domain/            User, Email (VO), RoleName (VO), excepciones de dominio
├── Application/       CreateUser, UpdateUser, ChangeUserRole, DeleteUser, RegisterApplicant
│   └── Port/          UserRepository, RoleAssigner, PasswordHasher
└── Infrastructure/    EloquentUserRepository, SpatieRoleAssigner, LaravelPasswordHasher
```

- Se elimina `RoleService` (solo reenvía llamadas) y el modelo `App\Models\Role` duplicado.
- Controladores web y API delegan en los mismos casos de uso.
- Autorización con **permisos** de Spatie (no nombres de rol en las rutas), asignados a
  cada rol según FUNCTIONAL_SPECIFICATIONS.md §7, y Policies para el alcance por
  programa del Coordinador (`doctorado_id`, donde 3 cubre ambos).
- Estado de cuenta (`activo`, `inactivo`, `suspendido`) como enum; un usuario
  suspendido no puede iniciar sesión.
- Registro público: completa los campos obligatorios y asigna el rol Aspirante.
- Corrige los defectos #6, #7, #8, #10 y #27 del inventario.
- Cambios de comportamiento confirmados por negocio: el Director pasa a solo lectura
  y el Asistente deja de poder calificar.

## Fase 3: Academic (resumen)

- Tabla de programas con `min_passing_grade` (48 por defecto) vinculada a `doctorados`.
- Estados de lapso e inscripción como enums (§10), con migración de datos de
  `'1'/'0'` e `Inscrito/Aprobado/Reprobado`.
- `CloseLapse` como caso de uso: solo inscripciones activas de ese lapso; guarda el
  resultado; un lapso cerrado no admite cambios.
