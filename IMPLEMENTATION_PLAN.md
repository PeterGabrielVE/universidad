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
| 3d | Expediente del estudiante: edición (#14) y alta (#15) | ✅ Completada |
| 4 | DoctoralSubmissions: tablas de entregas, asignación de jurados, carga y reemplazo, estados de entrega, nota ponderada 0–100 | Siguiente |
| 5 | Reporting: constancias, récord académico, actas y PDFs corregidos | Pendiente |
| 6 | Modernización de dependencias con justificación individual | Pendiente |
| 7 | Documentación final: README y revisión | Pendiente |

Admisiones (requisitos del Aspirante) y Finanzas (FUNCTIONAL_SPECIFICATIONS.md §1)
se planificarán cuando tengan requisitos detallados: no se implementan sobre suposiciones.

La arquitectura y los módulos están documentados en ARCHITECTURE.md.

## Fase 4: DoctoralSubmissions (entregas doctorales)

Reglas ya confirmadas: §4 (Post1 25 %, Post2 25 %, Presentación 50 %), §8 (escala
0–100), §9 (PDF/DOC/DOCX hasta 25 MB), §10 (estados borrador → en_revision →
aprobado/reprobado), §6–§7 (el Administrador reasigna jurados; el Docente califica las
entregas asignadas; el Coordinador las ve en su programa).

1. Tablas de entregas (hoy el código usa `posts_1`, `posts_2`, `presentations`, que no
   existen) y almacenamiento privado ya preparado en la Fase 0.
2. Asignación de jurados (Docentes) a cada entrega o estudiante.
3. Ciclo de la entrega y calificación 0–100 por el jurado asignado.
4. Nota final ponderada contra la nota mínima del programa.
5. Retirar los ~20 métodos heredados de `EstudiantesController` sobre entregas (#21–#23, #26).

Preguntas para negocio antes de empezar: ¿cuántos jurados califican cada entrega y
cómo se combinan sus notas? ¿Las entregas pertenecen al estudiante en un programa
(doble titulación = entregas separadas por programa)?
