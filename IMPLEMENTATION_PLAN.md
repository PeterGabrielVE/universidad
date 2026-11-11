# Plan de implementación

Evolución incremental, sin reescritura. Al cerrar cada fase: pruebas en verde,
PROJECT_STATUS.md actualizado y riesgos revisados.

| Fase | Objetivo | Estado |
|---|---|---|
| 0 | Cerrar los hallazgos críticos de seguridad (C1–C8) | ✅ Completada |
| 1 | Base de pruebas: factories de clase, pruebas de caracterización de lo que funciona | ✅ Completada |
| 2 | IdentityAccess: `User` único, roles en Spatie, matriz RBAC, estado de cuenta, registro de Aspirantes, CRUD de usuarios y roles | ✅ Completada |
| 3a | Academic: facultades → programas, `min_passing_grade`, lapsos por programa, estados, cierre correcto; cédula única | ✅ Completada |
| 3b | Academic: usuario ↔ programas, gestión de facultades y programas, inscripción, notas, kardex, expediente, pensum | Siguiente |
| 4 | DoctoralSubmissions: tablas de entregas, asignación de jurados, carga y reemplazo, estados de entrega, nota ponderada 0–100 | Pendiente |
| 5 | Reporting: constancias, récord académico, actas y PDFs corregidos | Pendiente |
| 6 | Modernización de dependencias con justificación individual | Pendiente |
| 7 | Documentación final: README y revisión | Pendiente |

Admisiones (requisitos del Aspirante) y Finanzas (FUNCTIONAL_SPECIFICATIONS.md §1)
se planificarán cuando tengan requisitos detallados: no se implementan sobre suposiciones.

La arquitectura y los módulos están documentados en ARCHITECTURE.md.

## Fase 3b: Academic (segunda parte)

1. **Usuario ↔ programas** (pendiente de confirmar, FUNCTIONAL_SPECIFICATIONS.md §14):
   tabla `program_user` en lugar del comodín `doctorado_id = 3`; `ProgramScope` pasa a
   recibir la lista de programas del usuario. Desbloquea el punto 2.
2. **Gestión de facultades y programas** (Administrador): alta, edición y nota mínima.
3. **Inscripción** (#18): el Asistente o el Coordinador inscribe a un estudiante en las
   asignaturas de un lapso abierto de su programa (`pre_inscrito` → `activo`).
4. **Registro de notas por materia** (#19): 0–100, solo en lapsos abiertos. Sin esto no
   se puede cerrar un lapso con inscripciones activas.
5. **Kardex / récord académico** (#20): historial por lapso con nota y resultado fijado.
6. **Expediente** (#14, #15): corregir la edición y el alta del expediente del estudiante.
7. **Pensum** (#28): consulta de asignaturas por semestre.

Preguntas para negocio antes de los puntos 3 y 4: ¿quién registra las notas por
materia (Docente de la asignatura, Coordinador, Control de Estudios)? ¿Las asignaturas
(`courses`) pertenecen a un programa o son comunes?
