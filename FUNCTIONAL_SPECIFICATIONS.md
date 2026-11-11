# Especificaciones funcionales

Reglas de negocio confirmadas por el responsable del producto (10-11-2026).
Cualquier regla nueva o cambio se registra aquí antes de implementarse.

## 1. Alcance

Sistema de gestión de **postgrado universitario** (doctorados). Los módulos de
educación escolar que aparecían en el panel (PPFF, grados, paralelos, turnos,
niveles) quedan **descartados**.

| Módulo | Contenido |
|---|---|
| Admisiones e Inscripciones | Aspirantes, carga de requisitos, matriculación |
| Control de Estudios | Facultades, programas, pensum, asignaturas, cohortes/lapsos |
| Gestión de Entregas y Aulas | Núcleo actual: tesis, proyectos de investigación, revisiones y calificaciones |
| Gestión de Usuarios | Roles: Administrativos, Docentes/Jurados, Estudiantes, Aspirantes |
| Finanzas y Aranceles | Pagos de matrícula, mensualidades y derechos de grado |
| Reportes y Actas | Constancias de estudio, récord académico, actas de calificación |

## 2. Programas de doctorado (`users.doctorado_id`)

| Id | Significado |
|---|---|
| 1 | Doctorado en Gerencia Avanzada |
| 2 | Doctorado en Ciencias de la Educación |
| 3 | Ambos programas: estudiante con doble inscripción, o coordinador/docente con visibilidad sobre las entregas de las dos facultades |

## 3. Aprobación y cierre de lapsos

- **Nota mínima aprobatoria:** 48/100 por defecto, configurable **por facultad o
  programa** mediante el campo `min_passing_grade`. No debe estar fija en el código.
- **Cierre de lapso:** solo afecta a las inscripciones activas de ese lapso
  (`lapse_id = ? AND status = 'activo'`). Las inscripciones de lapsos anteriores
  son **inmutables**: forman el récord académico.

## 4. Nota final de una entrega doctoral

Coeficientes de ponderación (suman 8):

| Etapa | Peso | % |
|---|---|---|
| Post1: primer avance o revisión | 2 | 25 % |
| Post2: segundo avance o artículo publicado | 2 | 25 % |
| Presentación: defensa oral o presentación final | 4 | 50 % |

`Nota final = (Post1 × 2 + Post2 × 2 + Presentación × 4) / 8`

## 5. Registro público y admisión

- El registro público **permanece abierto**, con privilegios mínimos.
- Todo usuario registrado recibe el rol **Aspirante**.
- El Aspirante solo puede: ver la oferta académica, editar su perfil y subir los
  documentos de admisión (título de pregrado/maestría, cédula, etc.).
- **Transición:** cuando Control de Estudios verifica documentos y pago, un
  administrador cambia el rol a **Estudiante** y le asigna cohorte y programa.

## 6. Matriz de permisos (RBAC)

| Módulo | Administrador (Control de Estudios) | Docente / Jurado | Estudiante | Aspirante |
|---|---|---|---|---|
| Usuarios | Crear, editar, suspender, cambiar roles | Ver alumnos asignados | — | — |
| Configuración (lapsos, notas) | Crear, abrir y cerrar lapsos | — | — | — |
| Documentos / entregas | Ver todos, reasignar jurados | Ver entregas de sus alumnos | Subir entregas, ver historial propio | Subir requisitos de ingreso |
| Calificaciones | Ver y generar actas | Calificar entregas asignadas | Ver sus notas | — |
| Pagos / aranceles | Validar pagos, generar reportes | — | Reportar pagos, ver estado | Pagar inscripción |

## 7. Roles del sistema

Confirmado el 11-11-2026. "Programa" se refiere a `users.doctorado_id` (§2): el 3
da alcance sobre ambos programas.

| Rol | Naturaleza | Puede | No puede |
|---|---|---|---|
| **Administrador** | Control de Estudios / superadmin | Todo: usuarios, configuración, lapsos, pagos, reportes globales | — |
| **Director** | Alta gerencia, supervisión | Lectura amplia de reportes, estadísticas y auditoría; aprobación final de actas de grado | Operar el día a día (matricular, editar usuarios) |
| **Coordinador** | Administrativo + académico, **limitado a su programa** | Asignar jurados, abrir/cerrar cohortes de su programa, ver notas de sus alumnos | Configurar otras facultades, finanzas globales |
| **Asistente** | Administrativo operativo | Verificar pagos y transferencias, validar documentos de identidad, asistir en la inscripción | Alterar notas, cerrar lapsos, gestionar jurados |
| **Docente / Jurado** | Académico | Ver y calificar las entregas asignadas, ver sus alumnos | Lo demás |
| **Estudiante** | — | Subir entregas, ver historial y notas propias, reportar pagos | — |
| **Aspirante** | — | Ver oferta, editar perfil, subir requisitos de ingreso, pagar inscripción | — |

Docente/Jurado y Aspirante son roles nuevos (no existen hoy en `roles`).

## 8. Calificaciones

- Escala **numérica 0–100** en base de datos (campo `score`) para asignaturas y entregas.
- El backend calcula la nota final ponderada (§4) y la compara con
  `min_passing_grade` del programa (§3).
- La interfaz muestra número y estado: `85/100 (Aprobado)`, `40/100 (Reprobado)`.
- **Decisión técnica:** al cerrar un lapso se guarda el resultado (aprobado/reprobado)
  de cada inscripción. Así, si un programa cambia después su `min_passing_grade`,
  el récord histórico no se recalcula (§3: histórico inmutable).

## 9. Archivos

| Módulo | Formatos | Tamaño máximo |
|---|---|---|
| Entregas académicas (tesis, Post1, Post2, presentación) | PDF, DOC, DOCX | 25 MB |
| Requisitos administrativos (cédula, recibos de pago, títulos) | PDF, JPG, PNG | 5 MB |

Los pósters y certificados de la presentación siguen hoy en el formulario de
entregas académicas: se les aplica la regla de entregas hasta que se confirme otra cosa.

## 10. Estados por entidad

Vocabulario cerrado. Se implementa con *backed enums* de PHP sobre columnas `string`:
el proyecto usa MySQL, y un ENUM nativo exigiría `ALTER TABLE` para cada valor nuevo.

| Entidad | Estados |
|---|---|
| Usuario / cuenta | `activo`, `inactivo`, `suspendido` |
| Inscripción (estudiante en un lapso) | `pre_inscrito` (falta pago/validación), `activo` (cursando), `congelado` (retiro temporal), `cerrado` (terminó el lapso) |
| Lapso / cohorte | `planificacion`, `abierto`, `cerrado` (notas definitivas, no admite cambios) |
| Entrega de documentos | `borrador`, `en_revision` (asignada a jurado), `aprobado`, `reprobado` |

Correspondencia con los datos actuales: lapso `'1'` → `abierto`, `'0'` → `cerrado`;
inscripción `Inscrito` → `activo`, `Aprobado`/`Reprobado` → `cerrado` con su resultado.

## 11. Decisiones de implementación (Fase 2)

Derivadas de las reglas anteriores; confirmar si alguna no encaja con el negocio.

- **Roles del sistema protegidos:** los 7 roles de §7 no se pueden renombrar ni
  eliminar, porque sostienen la matriz de permisos. Se pueden crear roles adicionales,
  que no tienen permisos.
- **Correo único sin distinguir mayúsculas:** `Ana@x.com` y `ana@x.com` son la misma cuenta.
- **Usuario sin rol:** ve una página de inicio que le indica contactar a Control de
  Estudios; no accede a ningún módulo.
- **Director:** además de reportes y estadísticas, puede ver el listado de usuarios,
  los expedientes de estudiantes y las notas (lectura amplia), pero no editarlos.

## 12. Puntos abiertos

1. **Facultad y programa:** ¿son la misma entidad (cada doctorado es una "facultad")
   o una facultad agrupa varios programas? Afecta a dónde vive `min_passing_grade`.
   Bloquea la Fase 3.
2. **Cédula única:** hoy solo el correo es único. ¿Debe impedirse registrar dos
   cuentas con la misma cédula (p. ej. un aspirante que se registra dos veces)?
