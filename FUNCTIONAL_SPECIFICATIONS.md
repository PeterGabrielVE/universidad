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

## 12. Decisiones de implementación (Fase 3a)

- **Cierre con notas pendientes:** un lapso no se cierra mientras tenga inscripciones
  activas sin nota; el sistema indica cuántas faltan. Las preinscritas y congeladas no
  se evalúan ni bloquean el cierre.
- **Ciclo del lapso:** `planificacion → abierto → cerrado`, sin retrocesos. Un lapso
  nuevo empieza en planificación o abierto, nunca cerrado.
- **Borrado:** solo se elimina un lapso en planificación; uno que se abrió tiene
  historia académica.
- **Lapsos anteriores a la Fase 3** (sin programa): solo los gestiona el Administrador
  y al cerrarlos se usa la nota base 48.
- **Nota mínima de los programas existentes:** 48 (la base confirmada); cada programa
  puede cambiarla. Los datos demo usan 75 en Gerencia para mostrar la diferencia.

## 13. Estructura académica e identidad

Confirmado el 11-11-2026.

- **Facultad → programas (1:N).** Una facultad agrupa varios programas (doctorados,
  maestrías…). La tabla histórica `doctorados` es la tabla de programas.
- **`min_passing_grade` vive en el programa:** las exigencias varían por nivel
  (p. ej. 75 en un doctorado frente a una maestría de la misma facultad).
- **El Coordinador se asigna al programa** (o a sus cohortes), no a la facultad. El
  control de una facultad completa sería un Decano, rol que hoy no se necesita.
- **La cédula es única** a nivel de base de datos: es el ancla de la identidad y evita
  que alguien con historial (reprobado, con deudas) se registre de nuevo con otro correo.
- **Pérdida de acceso al correo:** Control de Estudios actualiza el correo de la cuenta
  existente tras validar la cédula; nunca se crea un perfil paralelo.
- **Migración:** si existen cédulas repetidas, la migración se detiene y
  `php artisan users:duplicate-identification-cards` las lista. Fusionarlas o
  eliminarlas es una decisión de Control de Estudios; la migración no borra datos.

## 14. Decisiones de implementación (Fase 3b)

- **Programas en uso:** un programa con usuarios o lapsos no se elimina (son historia
  académica). Su pensum se borra con él si no está en uso.
- **Facultades con programas** no se eliminan: hay que mover o eliminar antes sus programas.
- **Cambiar la nota mínima** de un programa solo afecta a los cierres futuros; los
  resultados de lapsos cerrados ya están guardados (§8).
- **Semestre de la asignatura:** sigue siendo un dato de la asignatura, común a todos
  los pensums donde aparece. Si una materia transversal se dicta en semestres distintos
  según el programa, habría que mover el semestre a la relación programa ↔ asignatura.
- **Columnas obsoletas:** `users.doctorado_id` queda en NULL tras migrar al pivote, igual
  que `users.rol_id`; se eliminarán cuando se confirme que nada externo las lee.

## 15. Programas, pensum y calificaciones por materia

Confirmado el 11-11-2026.

- **Usuario ↔ programas (N:M)** mediante `program_user`. Sirve a estudiantes (doble
  titulación) y coordinadores (varios programas). El comodín `doctorado_id = 3` se
  eliminó: los usuarios con 3 pasaron a los programas 1 y 2. Con esto se habilita la
  creación de programas desde la aplicación.
- **Pensum (N:M)** mediante `program_course` (opción A): una asignatura transversal
  (Epistemología, Metodología de la Investigación) existe una vez y pertenece a varios
  programas.
- **Registro de notas por materia** (se implementa en la Fase 3c):
  1. **El docente titular** de la asignatura es el único que registra las notas de sus
     alumnos mientras el lapso o la materia estén abiertos.
  2. Cuando termina, **cierra el acta** y pierde el permiso de edición.
  3. **El Coordinador o Control de Estudios** cierran formalmente el lapso completo.
  4. Una **corrección posterior al cierre** solo la hace Control de Estudios
     (Administrador), con **registro de auditoría**.

## 16. Secciones, actas y correcciones

Confirmado el 11-11-2026.

- **Lapso → Asignatura → Sección → Docente.** Una asignatura puede dictarse en varias
  secciones simultáneas en un lapso; cada sección tiene un único docente titular.
- **Corrección de notas:** la justificación es obligatoria (`grade_audit_logs.justification`
  NOT NULL), p. ej. "Error de transcripción", "Resolución de apelación N° XXX".
- **Cierre de lapso estricto:** no se cierra con actas abiertas; ese cierre consolida el
  récord y habilita la inscripción del siguiente periodo.
- **Cierre administrativo forzoso:** el Administrador cierra un acta pendiente bajo su
  autoría para que el flujo continúe.

## 17. Decisiones de implementación (Fase 3c)

- **Quién abre secciones:** Administrador y Coordinador (en sus programas),
  permiso `sections.manage`. La asignatura debe estar en el pensum del programa del lapso
  y el docente debe ser un usuario activo con rol Docente.
- **Inscripción en dos pasos:** el Asistente o el Coordinador inscribe al estudiante en
  una sección (`pre_inscrito`) y la confirma (`activo`) cuando valida pago y documentos.
  Solo las inscripciones activas aparecen en el acta y se evalúan. Un estudiante se
  inscribe una sola vez por asignatura y lapso, y debe pertenecer al programa del lapso.
- **Cerrar el acta (docente)** exige que todos los estudiantes activos tengan nota.
- **El cierre forzoso no exige notas completas:** las que falten las registra Control de
  Estudios como correcciones auditadas.
- **Correcciones:** solo con el acta cerrada (con el acta abierta, la nota la pone el
  docente). Si el lapso ya está cerrado, el resultado se recalcula con la **misma nota
  mínima usada al cerrar** (`lapses.passing_grade_applied`), no con la actual del programa.
- **La auditoría guarda** nota y resultado anterior y nuevo, justificación, quién y
  cuándo, y una copia del nombre de quien corrigió (el registro sobrevive aunque se
  elimine la cuenta).
- **Inscripciones anteriores a la Fase 3c** (sin sección) se conservan y aparecen en el
  kardex; no se pueden corregir por auditoría.

## 18. Puntos abiertos

Ninguno bloqueante a 11-11-2026.
