# Plan de implementación

Evolución incremental, sin reescritura. Al cerrar cada fase: pruebas en verde,
PROJECT_STATUS.md actualizado y riesgos revisados.

| Fase | Objetivo | Estado |
|---|---|---|
| 0 | Cerrar los hallazgos críticos de seguridad (C1–C8) | ✅ Completada |
| 1 | Base de pruebas: factories de clase, pruebas de caracterización de lo que funciona | ✅ Completada |
| 2 | IdentityAccess: `User` único, roles en Spatie, matriz RBAC, estado de cuenta, registro de Aspirantes, CRUD de usuarios y roles | ✅ Completada |
| 3 | Academic: facultades/programas con `min_passing_grade`, lapsos por programa, cierre correcto, inscripción, notas, kardex, pensum | Siguiente |
| 4 | DoctoralSubmissions: tablas de entregas, asignación de jurados, carga y reemplazo, estados de entrega, nota ponderada 0–100 | Pendiente |
| 5 | Reporting: constancias, récord académico, actas y PDFs corregidos | Pendiente |
| 6 | Modernización de dependencias con justificación individual | Pendiente |
| 7 | Documentación final: README y revisión | Pendiente |

Admisiones (requisitos del Aspirante) y Finanzas (FUNCTIONAL_SPECIFICATIONS.md §1)
se planificarán cuando tengan requisitos detallados: no se implementan sobre suposiciones.

La arquitectura objetivo y el módulo IdentityAccess están documentados en ARCHITECTURE.md.

## Fase 3: Academic

Módulo `app/Modules/Academic` con la misma estructura que IdentityAccess.

1. **Programas:** `doctorados` pasa a ser el programa académico, con
   `min_passing_grade` (48 por defecto) (§3). Facultades si se confirman como entidad
   aparte.
2. **Lapsos por programa:** `lapses.program_id` para que el Coordinador abra y cierre
   cohortes de su programa (§7). Permiso nuevo `lapses.manage_own_program`.
3. **Estados como enums (§10):** lapso `planificacion | abierto | cerrado`;
   inscripción `pre_inscrito | activo | congelado | cerrado`. Migración de datos:
   `'1'` → `abierto`, `'0'` → `cerrado`, `Inscrito` → `activo`,
   `Aprobado/Reprobado` → `cerrado` con su resultado.
4. **`CloseLapse`:** solo inscripciones `activo` de ese lapso; calcula y **guarda** el
   resultado contra `min_passing_grade`; un lapso cerrado no admite cambios. Corrige #17.
5. **Inscripción, notas y kardex** (#18–#20) y edición del expediente (#14, #15).
6. Pensum (#28): pantalla de solo lectura por semestre, o retirar el código muerto
   si negocio no lo necesita.

Punto a confirmar antes de empezar: ¿"facultad" es una entidad distinta del programa
de doctorado, o son lo mismo en este sistema?
