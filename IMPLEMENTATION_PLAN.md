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
| 3c | Academic: oferta por lapso con docente titular, inscripción, acta de notas, corrección auditada, kardex, expediente | Siguiente |
| 4 | DoctoralSubmissions: tablas de entregas, asignación de jurados, carga y reemplazo, estados de entrega, nota ponderada 0–100 | Pendiente |
| 5 | Reporting: constancias, récord académico, actas y PDFs corregidos | Pendiente |
| 6 | Modernización de dependencias con justificación individual | Pendiente |
| 7 | Documentación final: README y revisión | Pendiente |

Admisiones (requisitos del Aspirante) y Finanzas (FUNCTIONAL_SPECIFICATIONS.md §1)
se planificarán cuando tengan requisitos detallados: no se implementan sobre suposiciones.

La arquitectura y los módulos están documentados en ARCHITECTURE.md.

## Fase 3c: Academic (tercera parte)

Reglas confirmadas en FUNCTIONAL_SPECIFICATIONS.md §15.

1. **Oferta de asignaturas por lapso** con su **docente titular** (asignatura del pensum del
   programa del lapso).
2. **Inscripción** (#18): el Asistente o el Coordinador inscribe a un estudiante en
   asignaturas ofertadas de un lapso abierto (`pre_inscrito` → `activo`).
3. **Acta de notas** (#19): el docente titular registra notas 0–100 mientras el lapso esté
   abierto y **cierra el acta**; después no puede editarla.
4. **Cierre de lapso** por Coordinador o Control de Estudios (ya implementado en 3a; se
   integra con las actas).
5. **Corrección posterior** solo por el Administrador, con registro de auditoría.
6. **Kardex** (#20) y **expediente** (#14, #15).

Preguntas abiertas en FUNCTIONAL_SPECIFICATIONS.md §16.
