# Estado del proyecto

Última actualización: 11-11-2026 · Fases completadas: **0 (seguridad)**, **1 (base de pruebas)**, **2 (IdentityAccess)**, **3a (estructura académica y lapsos)**, **3b (programas, pensum y usuario ↔ programas)** y **3c (secciones, inscripción, actas y kardex)**

## Avance real

Se cuenta como **verificada** una funcionalidad cubierta por pruebas automatizadas
que pasan. El avance no se mide por archivos creados.

| Métrica | Valor |
|---|---|
| Funcionalidades del inventario | 28 |
| Verificadas con pruebas | **19 de 28 (68 %)**: #1, #2, #4–#13, #16–#20, #27, #28 |
| Funcionalidades nuevas (fuera del inventario original) | Fase 2: estado de cuenta, inicio de Aspirante y Docente, alcance por programa. Fase 3a: facultades, nota mínima por programa, lapsos por programa gestionados por el Coordinador, cédula única. Fase 3b: usuario ↔ programas (N:M), gestión de facultades y programas, pensum N:M. Fase 3c: secciones con docente titular, cierre forzoso de actas, corrección auditada |
| Hallazgos críticos de seguridad cerrados y con prueba | 8 de 8 |
| Pruebas | **213 pasan** (606 aserciones): 85 unitarias, 128 de Feature |

Pendiente de **validación manual en el navegador**: las pantallas Vue de usuarios
(#6, #7). Su API y las vistas Blade que las montan están cubiertas por pruebas; el
render de Vue no (no hay pruebas de frontend).

## Inventario de funcionalidades

| # | Funcionalidad | Estado | Fase |
|---|---|---|---|
| 1 | Login / logout | ✅ Verificada (rechaza cuentas no activas) | 2 |
| 2 | Registro público | ✅ Verificada: crea un Aspirante activo; pide apellido y cédula | 2 |
| 3 | Recuperar contraseña | Falta validarla (SMTP) | 6 |
| 4 | Cambio de idioma | ✅ Verificada | — |
| 5 | Panel principal | ✅ Verificada (con `dashboard.view`; módulos según permiso y alcance confirmado) | 2 |
| 6 | Usuarios: listado (Vue) | ✅ Verificada (rol, estado, búsqueda) · falta prueba en navegador | 2 |
| 7 | Usuarios: crear | ✅ Verificada (Vue → API v1 y modal Blade) · falta prueba en navegador | 2 |
| 8 | Usuarios: editar | ✅ Verificada (datos, rol, programa, estado, contraseña opcional) | 2 |
| 9 | Usuarios: eliminar | ✅ Verificada | 2 |
| 10 | Usuarios filtrados por rol | ✅ Verificada (corregido) | 2 |
| 11 | Roles CRUD | ✅ Verificada (un solo camino; roles del sistema protegidos) | 2 |
| 12 | Permisos | ✅ Verificada como matriz en código (`AccessPolicy`); sin pantalla de edición, ver decisión en ARCHITECTURE.md | 2 |
| 13 | Estudiantes: listar, ver, eliminar | ✅ Verificada (con alcance por programa) | 3 |
| 14 | Estudiantes: editar | Con errores (columnas `first_name`, `cod_phone` inexistentes) | 3d |
| 15 | Estudiantes: completar expediente | Con errores (`$doc` indefinido, firma incorrecta) | 3d |
| 16 | Lapsos: CRUD | ✅ Verificada (por programa, estados `planificacion/abierto/cerrado`, fechas validadas) | 3a |
| 17 | Cierre de lapso | ✅ Verificada (corregido: solo inscripciones activas de ese lapso, nota mínima del programa, inmutable) | 3a |
| 18 | Inscripción por materias | ✅ Verificada: por sección, preinscripción y confirmación | 3c |
| 19 | Registro de notas por materia | ✅ Verificada: acta del docente titular, cierre del acta, cierre forzoso, corrección auditada | 3c |
| 20 | Kardex / récord académico | ✅ Verificada (estudiante: el suyo; personal: según permiso y programa) | 3c |
| 21 | Carga de entregas doctorales | Pendiente (faltan tablas `posts_1`, `posts_2`, `presentations`) | 4 |
| 22 | Edición de entregas | Con errores | 4 |
| 23 | Calificación de entregas | Con errores (sin tablas; pasará a escala 0–100) | 4 |
| 24 | PDF por sede | Con errores (tabla `sedes` no existe) | 5 |
| 25 | PDF por estudiante | Con errores (total siempre 8; debe usar la fórmula ponderada) | 5 |
| 26 | Descarga de documentos | Implementada con `documents.download`, desde disco privado | 4 |
| 27 | API v1 de usuarios | ✅ Verificada | 2 |
| 28 | Pensum | ✅ Verificada: pensum N:M por programa, asignaturas transversales compartidas | 3b |

Módulos en alcance aún sin requisitos detallados: Admisiones (carga de requisitos del
Aspirante), Finanzas y Aranceles, Constancias y Actas.

## Registro de fases

### Fase 3c: secciones, inscripción, actas y kardex (completada)

- **Secciones** (`sections`): Lapso → Asignatura (del pensum) → Sección → Docente titular. Varias secciones por asignatura; código único por asignatura y lapso.
- **Inscripción por sección** (#18) en dos pasos: preinscrito → activo. Entrada para el Asistente en "Secciones e inscripción".
- **Acta del docente** (#19): notas 0–100 de los estudiantes activos, solo el docente titular y con el lapso abierto; al cerrar el acta pierde la edición.
- **Cierre de lapso estricto:** bloqueado si hay actas abiertas. **Cierre administrativo forzoso** del Administrador.
- **Corrección auditada** (`grade_audit_logs`, justificación NOT NULL); si el lapso está cerrado recalcula el resultado con la nota mínima aplicada al cierre (`lapses.passing_grade_applied`).
- **Kardex** (#20) por estudiante.
- Permisos nuevos: `sections.manage` (Administrador, Coordinador), `grades.override` y `gradesheets.force_close` (Administrador).

**Incidente corregido en esta fase:** las pruebas corrían contra la base de **desarrollo** (`gabook`) desde la Fase 0. `docker-compose.yml` define `DB_DATABASE=gabook` en el entorno del contenedor y Laravel lee `$_SERVER` antes que el `<env force>` de `phpunit.xml`, así que `RefreshDatabase` vaciaba `gabook` en cada ejecución (por eso estaba vacía en la Fase 0). Corrección: `phpunit.xml` fuerza también `$_SERVER` y `Tests\TestCase` aborta si la base no termina en `_testing`. Se comprobó con una tabla testigo en `gabook` que sobrevivió a la suite completa. La base de desarrollo se restauró con los seeders; los datos creados a mano en ella se perdieron.

**Verificado además:** `DemoDataSeeder` (y su re-ejecución) con secciones y actas; flujo real en el navegador por HTTP (docente → acta, administrador → sección, estudiante → kardex); `route:cache`.

**Requiere** `migrate` y volver a ejecutar `RolesTableSeeder` (permisos nuevos).

### Fase 3b: programas, pensum y usuario ↔ programas (completada)

- **Fin del comodín `doctorado_id = 3`:** relación N:M `program_user`. La migración pasa cada 3 a los programas 1 y 2, cualquier otro valor a su programa, y deja `users.doctorado_id` en NULL. Reversible.
- `ProgramScope` trabaja con la lista de programas del usuario: un coordinador puede tener varios; un estudiante con doble titulación es visible para el coordinador de cualquiera de sus programas.
- Formularios de usuarios (Blade y Vue) con **selección múltiple de programas**.
- **Gestión de facultades y programas** (Administrador, `programs.manage`): alta, edición, nota mínima; no se eliminan programas con usuarios o lapsos ni facultades con programas.
- **Pensum N:M** (`program_course`, opción A): una asignatura transversal existe una vez y está en varios programas. Consulta para Director y Coordinador (`pensum.view`, el Coordinador solo los suyos). Corrige #28.
- Se corrigieron las lecturas del comodín en código heredado (sidebar del estudiante, reportes, calificación de doble titulación, actualización de entregas de Ciencias).
- Layout común `layouts/panel` para las pantallas nuevas.

**Verificado además:** migración del comodín con datos demo (usuario con 3 → programas [1, 2]); `route:cache`; build del frontend.

**Requiere** `migrate` y volver a ejecutar `RolesTableSeeder` (permisos `programs.manage` y `pensum.view`).

### Fase 3a: estructura académica y lapsos (completada)

Módulo hexagonal `app/Modules/Academic` (ver ARCHITECTURE.md).

- **Facultades → programas** (`faculties`, `doctorados.faculty_id`) y `min_passing_grade` por programa (48 por defecto).
- **Lapsos por programa** (`lapses.program_id`); el Coordinador crea, abre y cierra los de su programa (permiso `lapses.manage_program`), el Administrador todos.
- **Estados normalizados** (§10) con migración de datos reversible: lapsos `1/0` → `abierto/cerrado`; inscripciones `Inscrito` → `activo`, `Aprobado/Reprobado` → `cerrado` + `result`.
- **Cierre de lapso (#17) corregido:** antes el `UPDATE` cambiaba las inscripciones de todos los lapsos y la clase `Lapso_estudiante` no cargaba en Linux. Ahora solo evalúa las activas de ese lapso, contra la nota mínima del programa, guarda el resultado, exige que todas tengan nota y deja el lapso inmutable.
- **Cédula única** (dominio + índice `UNIQUE`). La migración se detiene si hay duplicados; `users:duplicate-identification-cards` los lista.
- Modal de edición de lapsos: muestra los errores (antes fallaba en silencio) y pide confirmación antes de cerrar.
- `TransactionManager` pasa al módulo compartido (`app/Modules/Shared`).

**Verificado además:** `migrate:fresh --seed` + `DemoDataSeeder`; ida y vuelta de las migraciones con datos en el formato antiguo; la migración se detiene ante una cédula repetida y continúa tras resolverla; `route:cache`.

**Requiere** volver a ejecutar `RolesTableSeeder` (permiso nuevo) y `migrate` en cada entorno.

### Fase 2: IdentityAccess (completada)

Arquitectura: primer módulo hexagonal, `app/Modules/IdentityAccess` (ver ARCHITECTURE.md).

**Funcionalidad**
- Matriz RBAC confirmada (FUNCTIONAL_SPECIFICATIONS.md §7) en `AccessPolicy`; rutas
  autorizadas por **permiso**. Roles nuevos: Docente (id 6) y Aspirante (id 7).
- Cambios de comportamiento confirmados: el **Director** pasa a solo lectura (ve
  usuarios, reportes, estudiantes y notas; no crea, edita ni configura); el
  **Asistente** ya no califica ni ve notas; el **Coordinador** solo ve estudiantes de
  su programa (3 = ambos) y no edita expedientes.
- Estado de cuenta `activo | inactivo | suspendido`: las cuentas no activas no inician
  sesión y la sesión se cierra si se suspenden estando conectadas.
- Registro público como Aspirante, con apellido y cédula (antes fallaba por columnas NOT NULL).
- Inicio propio para Aspirante (oferta académica) y Docente.
- Usuarios: listado con rol, estado, filtro por rol y búsqueda; alta y edición con
  rol, programa y estado; errores de negocio mostrados en su campo.
- Roles: los 7 roles del sistema no se pueden renombrar ni eliminar.
- Entregas académicas: solo PDF, DOC y DOCX hasta 25 MB (§9), y PHP configurado para
  aceptarlos (`docker/php/uploads.ini`). **Requiere `docker compose build app`.**
- Panel y sidebar: solo módulos en alcance; cada enlace según su permiso. El sidebar
  mostraba a todos los roles (también al estudiante) 14 enlaces que llevaban a reportes.

**Estructura**
- Un solo modelo `App\Models\User` (se eliminó `App\User`). Migración que actualiza
  `model_type` de Spatie y Sanctum y añade `users.status`.
- Roles solo en Spatie; `users.rol_id` deja de leerse y escribirse (columna conservada).
- Eliminados: `RoleService`, 5 UseCases de reenvío, `RoleRepository` + interfaz,
  `RepositoryServiceProvider` (no registrado), `App\Models\Role`, `App\Authorization\Roles`
  y los componentes Vue sin uso (`Form.vue`, `router/index.js`, `ExampleComponent.vue`).
- `admin@gmail.com` pasa a **Administrador** (era Director, ahora de solo lectura).

**Pruebas:** 36 unitarias nuevas (dominio y casos de uso con adaptadores en memoria) y
pruebas de Feature para la matriz, alcance, ciclo de cuenta y registro. Verificados
también: `migrate:fresh --seed` + `DemoDataSeeder`, rollback de la migración,
`route:cache` y build del frontend.

### Fase 1: base de pruebas (completada)

- Factories de clase (`UserFactory` con `withRole()`, `EstudianteFactory`, `LapsoFactory`).
- 30 pruebas de caracterización en `tests/Feature/Characterization`.
- Corregido: la ficha del estudiante daba 500 si no existía ningún lapso.

### Fase 0: seguridad (completada)

| Hallazgo | Corrección | Prueba |
|---|---|---|
| C1 `/generate-token` emitía un token del Director a cualquiera | Ruta eliminada; Vue usa la cookie de sesión (Sanctum SPA) | `RouteProtectionTest`, `ApiProtectionTest` |
| C2 Borrado de usuarios por GET y sin sesión | Sesión + permiso; verbo `DELETE` | `RouteProtectionTest` |
| C3 API de roles pública | `auth:sanctum` + permiso | `ApiProtectionTest` |
| C4 Subidas sin validar en `public/document` (ejecución remota) | Lista blanca, disco privado, nombre UUID | `DocumentUploadTest` |
| C5 ~40 rutas sin autenticación | Todas dentro de `auth` | `RouteProtectionTest` |
| C6 Sin autorización en el servidor | Permisos por ruta + `student.access` | `AuthorizationTest` |
| C7 Asignación masiva | Campos en lista blanca (FormRequest + caso de uso) | `AuthorizationTest` |
| C8 `/test-session` y excepciones en respuestas | Ruta eliminada; errores mapeados | `RouteProtectionTest`, `AuthorizationTest` |

Además: limitador `api` (permitía 0 peticiones), `route:cache` operativo, script de
arranque de Docker corregido, `*.sh` con LF.

## Riesgos y deuda pendiente

| Riesgo | Impacto | Mitigación prevista |
|---|---|---|
| Las 63 asignaturas sembradas son de un pensum de pregrado de ingeniería | No corresponden a los doctorados | Control de Estudios debe cargar las asignaturas reales; la migración no asigna ninguna a los programas |
| Pruebas de frontend inexistentes | Las pantallas Blade/Vue solo se verifican por HTTP | Valorar pruebas de navegador (Dusk) en la Fase 6 |
| Rollback de la migración académica | Elimina `program_id` de los lapsos (dato nuevo de la Fase 3a) | Es reversible en estructura; usar solo en desarrollo |
| La restricción del Docente a "sus" alumnos depende de la asignación de jurados | Hoy un Docente con `grades.record` califica a cualquier estudiante | Fase 4: asignación de jurados |
| `EstudiantesController` (~1.200 líneas) sigue siendo MVC heredado | Deuda de mantenimiento | Fases 3–5 |
| Laravel 10 y PHP 8.1 sin soporte de seguridad | Vulnerabilidades sin parche | Fase 6 |
| axios 0.19.2 con CVEs conocidos; `vue-router` sin uso en `package.json` | Riesgo en el frontend | Fase 6 |
| Columna `users.rol_id` sin uso | Confusión | Eliminarla con migración cuando se confirme que nada externo la lee |
| La base de desarrollo `gabook` no tiene datos ni la migración de la Fase 2 | No se validó en el navegador | `docker compose build app` (aplica migraciones al arrancar) + seeders (README) |
