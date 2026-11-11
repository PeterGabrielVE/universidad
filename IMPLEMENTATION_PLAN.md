# Plan de implementación

Evolución incremental, sin reescritura. Al cerrar cada fase: pruebas en verde,
PROJECT_STATUS.md actualizado y riesgos revisados.

| Fase | Objetivo | Estado |
|---|---|---|
| 0 | Cerrar los hallazgos críticos de seguridad (C1–C8) | ✅ Completada |
| 1 | Base de pruebas: factories de clase, pruebas de caracterización de lo que funciona | ✅ Completada |
| 2 | IdentityAccess: `User` único, roles en Spatie, matriz RBAC, estado de cuenta, registro de Aspirantes, CRUD de usuarios y roles | ✅ Completada |
| 3a | Academic: facultades → programas, `min_passing_grade`, lapsos por programa, estados, cierre correcto; cédula única | ✅ Completada |
| 3b | Academic: usuario ↔ programas, gestión de facultades y programas, pensum N:M | ✅ Completada |
| 3c | Academic: secciones con docente titular, inscripción, acta de notas, cierre forzoso, corrección auditada, kardex | ✅ Completada |
| 3d | Expediente del estudiante: corregir edición (#14) y alta (#15) | Siguiente |
| 4 | DoctoralSubmissions: tablas de entregas, asignación de jurados, carga y reemplazo, estados de entrega, nota ponderada 0–100 | Pendiente |
| 5 | Reporting: constancias, récord académico, actas y PDFs corregidos | Pendiente |
| 6 | Modernización de dependencias con justificación individual | Pendiente |
| 7 | Documentación final: README y revisión | Pendiente |

Admisiones (requisitos del Aspirante) y Finanzas (FUNCTIONAL_SPECIFICATIONS.md §1)
se planificarán cuando tengan requisitos detallados: no se implementan sobre suposiciones.

La arquitectura y los módulos están documentados en ARCHITECTURE.md.

## Fase 3d: expediente del estudiante

1. **Edición (#14):** hoy escribe columnas inexistentes (`first_name`, `cod_phone`,
   `lapses_students.doctorado_id`) y falla siempre. Separar lo que edita el estudiante
   (datos de contacto) de lo que asigna Control de Estudios (programa, cohorte).
2. **Alta (#15):** `$doc` indefinido y llamada con firma incorrecta; depende de las tablas
   de entregas de la Fase 4, así que se separa el alta del expediente de la carga de
   documentos.

Pregunta para negocio: ¿qué datos del expediente puede editar el propio estudiante?
