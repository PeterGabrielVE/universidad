# Estado del proyecto

Última actualización: 11-11-2026 · Fases completadas: **0 (seguridad)**, **1 (base de pruebas)** y **2 (IdentityAccess)**

## Avance real

Se cuenta como **verificada** una funcionalidad cubierta por pruebas automatizadas
que pasan. El avance no se mide por archivos creados.

| Métrica | Valor |
|---|---|
| Funcionalidades del inventario | 28 |
| Verificadas con pruebas | **14 de 28 (50 %)**: #1, #2, #4–#13, #16, #27 |
| Funcionalidades nuevas de la Fase 2 (fuera del inventario original) | Estado de cuenta, inicio de Aspirante y Docente, alcance por programa del Coordinador |
| Hallazgos críticos de seguridad cerrados y con prueba | 8 de 8 |
| Pruebas | **131 pasan** (332 aserciones): 36 unitarias, 95 de Feature |

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
| 14 | Estudiantes: editar | Con errores (columnas `first_name`, `cod_phone` inexistentes) | 3 |
| 15 | Estudiantes: completar expediente | Con errores (`$doc` indefinido, firma incorrecta) | 3 |
| 16 | Lapsos: CRUD | ✅ Verificada (sin validación de entrada) | 3 |
| 17 | Cierre de lapso | Con errores (afecta a todos los lapsos; clase mal escrita) | 3 |
| 18 | Inscripción por materias | Pendiente | 3 |
| 19 | Registro de notas por materia | Pendiente | 3 |
| 20 | Kardex / récord académico | Pendiente | 3 |
| 21 | Carga de entregas doctorales | Pendiente (faltan tablas `posts_1`, `posts_2`, `presentations`) | 4 |
| 22 | Edición de entregas | Con errores | 4 |
| 23 | Calificación de entregas | Con errores (sin tablas; pasará a escala 0–100) | 4 |
| 24 | PDF por sede | Con errores (tabla `sedes` no existe) | 5 |
| 25 | PDF por estudiante | Con errores (total siempre 8; debe usar la fórmula ponderada) | 5 |
| 26 | Descarga de documentos | Implementada con `documents.download`, desde disco privado | 4 |
| 27 | API v1 de usuarios | ✅ Verificada | 2 |
| 28 | Pensum | Código muerto | 3 |

Módulos en alcance aún sin requisitos detallados: Admisiones (carga de requisitos del
Aspirante), Finanzas y Aranceles, Constancias y Actas.

## Registro de fases

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
| Lapsos sin programa: el Coordinador aún no puede abrir/cerrar cohortes de su programa | Hoy solo el Administrador gestiona lapsos | Fase 3: programas/facultades y lapsos por programa |
| La restricción del Docente a "sus" alumnos depende de la asignación de jurados | Hoy un Docente con `grades.record` califica a cualquier estudiante | Fase 4: asignación de jurados |
| `EstudiantesController` (~1.200 líneas) sigue siendo MVC heredado | Deuda de mantenimiento | Fases 3–5 |
| Laravel 10 y PHP 8.1 sin soporte de seguridad | Vulnerabilidades sin parche | Fase 6 |
| axios 0.19.2 con CVEs conocidos; `vue-router` sin uso en `package.json` | Riesgo en el frontend | Fase 6 |
| Columna `users.rol_id` sin uso | Confusión | Eliminarla con migración cuando se confirme que nada externo la lee |
| La base de desarrollo `gabook` no tiene datos ni la migración de la Fase 2 | No se validó en el navegador | `docker compose build app` (aplica migraciones al arrancar) + seeders (README) |
