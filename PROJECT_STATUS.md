# Estado del proyecto

Última actualización: 11-11-2026 · Fases completadas: **0 (seguridad)** y **1 (base de pruebas)**

## Avance real

Se cuenta como **verificada** una funcionalidad cubierta por pruebas automatizadas
que pasan. El avance no se mide por archivos creados.

| Métrica | Valor |
|---|---|
| Funcionalidades del inventario | 28 |
| Verificadas con pruebas | **6 de 28 (21 %)**: #1, #4, #5, #9, #13, #16 |
| Parcialmente verificadas | #7 (alta desde el modal Blade), #11 (roles) |
| Hallazgos críticos de seguridad cerrados y con prueba | 8 de 8 |
| Pruebas | 71 pasan, 1 incompleta (defecto #10), 156 aserciones |

Las Fases 0 y 1 no añaden funcionalidades nuevas: protegen accesos y verifican lo
existente. Con la Fase 1 el 21 % estimado en la auditoría pasa a estar **verificado**.

**Distancia respecto a las reglas confirmadas (§7 de FUNCTIONAL_SPECIFICATIONS.md),
a cerrar en la Fase 2:** hoy el Director tiene CRUD de usuarios y el Asistente puede
calificar, porque los grupos de la Fase 0 reproducen el sidebar. Faltan los roles
Docente/Jurado y Aspirante, y limitar al Coordinador a su programa.

## Inventario de funcionalidades

| # | Funcionalidad | Estado | Fase prevista |
|---|---|---|---|
| 1 | Login / logout | ✅ Verificada | 2 |
| 2 | Registro público | Con errores (campos NOT NULL; debe asignar rol Aspirante) | 2 |
| 3 | Recuperar contraseña | Falta validarla (SMTP) | 2 |
| 4 | Cambio de idioma | ✅ Verificada | — |
| 5 | Panel principal | ✅ Verificada (solo personal desde la Fase 0) | — |
| 6 | Usuarios: listado (Vue) | Parcial (ya no usa token robado; falta columna `status` y mostrar el rol) | 2 |
| 7 | Usuarios: crear | Parcial (`Create.vue` llama a una URL inexistente; API v1 falla por `status`) | 2 |
| 8 | Usuarios: editar | Con errores (enlace `/users/{id}/edit` da 404) | 2 |
| 9 | Usuarios: eliminar | ✅ Verificada (protegida y con verbo DELETE) | 2 |
| 10 | Usuarios filtrados por rol | Con errores (la vista Vue carga todos los usuarios e ignora el filtro) | 2 |
| 11 | Roles CRUD | Parcial (dos caminos web/API; el formulario Blade no envía `guard_name`) | 2 |
| 12 | Permisos | Pendiente (ruta retirada: el controlador no existía) | 2 |
| 13 | Estudiantes: listar, ver, eliminar | ✅ Verificada (corregido el 500 sin lapsos) | 3 |
| 14 | Estudiantes: editar | Con errores (columnas `first_name`, `cod_phone` inexistentes) | 3 |
| 15 | Estudiantes: completar expediente | Con errores (`$doc` indefinido, firma incorrecta) | 3 |
| 16 | Lapsos: CRUD | ✅ Verificada (sin validación de entrada) | 3 |
| 17 | Cierre de lapso | Con errores (afecta a todos los lapsos; clase mal escrita) | 3 |
| 18 | Inscripción por materias | Pendiente (ruta retirada: el controlador no existía) | 3 |
| 19 | Registro de notas por materia | Pendiente | 3 |
| 20 | Kardex / récord académico | Pendiente | 3 |
| 21 | Carga de entregas doctorales | Pendiente (faltan tablas `posts_1`, `posts_2`, `presentations`) | 4 |
| 22 | Edición de entregas | Con errores | 4 |
| 23 | Calificación de entregas | Con errores (sin tablas) | 4 |
| 24 | PDF por sede | Con errores (tabla `sedes` no existe) | 5 |
| 25 | PDF por estudiante | Con errores (total siempre 8; debe usar la fórmula ponderada) | 5 |
| 26 | Descarga de documentos | Implementada para el personal, desde disco privado | 4 |
| 27 | API v1 de usuarios | Con errores (`status`) | 2 |
| 28 | Pensum | Código muerto | 3 |

Módulos nuevos en alcance (ver FUNCTIONAL_SPECIFICATIONS.md): Admisiones, Finanzas,
Actas. Aún no tienen requisitos detallados.

## Registro de fases

### Fase 0: seguridad (completada)

| Hallazgo | Corrección | Prueba |
|---|---|---|
| C1 `/generate-token` emitía un token del Director a cualquiera | Ruta eliminada; las pantallas Vue usan la cookie de sesión (Sanctum en modo SPA) | `RouteProtectionTest`, `ApiProtectionTest` |
| C2 Borrado de usuarios por GET y sin sesión | Requiere sesión y rol admin; verbo `DELETE` | `RouteProtectionTest` |
| C3 API de roles pública | `auth:sanctum` + rol admin | `ApiProtectionTest` |
| C4 Subidas sin validar en `public/document` (ejecución remota) | Lista blanca de tipos, 10 MB, disco privado `storage/app/documents`, nombre UUID, extensión deducida del contenido | `DocumentUploadTest` |
| C5 ~40 rutas sin autenticación | Todas dentro de `auth`, agrupadas por rol | `RouteProtectionTest` |
| C6 Sin autorización en el servidor | Middleware de rol de Spatie por grupo de rutas y `student.owner` para expedientes | `AuthorizationTest` |
| C7 Asignación masiva (`$request->all()`) | Campos en lista blanca; `rol_id` y rol de Spatie sincronizados | `AuthorizationTest` |
| C8 `/test-session` y mensajes de excepción en respuestas | Ruta eliminada; el detalle va al log | `RouteProtectionTest`, `AuthorizationTest` |

Bugs encontrados durante la fase y corregidos:

- `throttle:api` sin limitador definido permitía **0 peticiones**: la segunda llamada a
  la API en el mismo minuto daba 429 (p. ej. listar roles y luego eliminar).
- `route:list` y `route:cache` fallaban: rutas a controladores inexistentes
  (`PermissionController`, `InscripcionController`) y nombres duplicados
  (`login`, `register`, `roles.*` entre web y API).
- La ruta `edit/documentPres` apuntaba a `EAstudiantesController`.
- El enlace "Roles" del sidebar apuntaba a una ruta POST.
- `wait-for-mysql.sh` no esperaba a MySQL (`&>` en `sh` manda el comando a segundo
  plano y la imagen no tiene cliente `mysql`) y regeneraba `APP_KEY` en cada arranque.
  **Requiere `docker compose build app` para aplicarse.**
- `*.sh` forzados a finales de línea LF (`.gitattributes`).

Rutas retiradas (apuntaban a controladores que no existen): `permission.*`,
`getLapso/{id}`, `POST updateLapso` (sin id), `/test-session`, `/generate-token`.

### Fase 1: base de pruebas (completada)

- Factories de clase en `database/factories` (`UserFactory` con `withRole()`,
  `EstudianteFactory`, `LapsoFactory`) en lugar de la sintaxis legacy, que no
  funcionaba en Laravel 10. `Database\Factories` pasa a PSR-4 en `composer.json`.
- 30 pruebas de caracterización en `tests/Feature/Characterization`: login/logout,
  idioma, panel, CRUD de lapsos, roles, alta y borrado de usuarios, estudiantes.
- Se retiraron las dos pruebas de ejemplo de Laravel (no verificaban nada útil).

Defectos encontrados por las pruebas de caracterización:

- **Corregido:** la ficha y la edición del estudiante daban 500 si no existía ningún
  lapso (instalación nueva). Ahora el campo queda vacío.
- **Pendiente (Fase 2):** los filtros de usuarios por rol (#10) ya no filtran. Prueba
  marcada como incompleta con referencia a este defecto.

## Riesgos y deuda pendiente

| Riesgo | Impacto | Mitigación prevista |
|---|---|---|
| Dos modelos `User` (`App\User` y `App\Models\User`) sobre la misma tabla | Los roles de Spatie se guardan con `model_type = App\User`; un token Bearer resolvería `App\Models\User` sin roles | Unificar en la Fase 2 |
| `users.rol_id` y roles de Spatie: dos fuentes de verdad | Inconsistencias si se escribe una sin la otra | Spatie como única fuente en la Fase 2 |
| Laravel 10 y PHP 8.1 sin soporte de seguridad | Vulnerabilidades sin parche | Laravel 10.48.x reciente en la Fase 6; plan aparte para PHP 8.3 + Laravel 11/12 |
| axios 0.19.2 con CVEs conocidos | Riesgo en el frontend | Actualizar en la Fase 6 |
| El `node_modules` local tiene laravel-mix 5.0.9 y el lock pide 6.0.49 | Builds locales fallan | `npm ci` (ver TESTING.md) |
| Grupos de rol provisionales (`app/Authorization/Roles.php`) | Pueden no coincidir con la matriz RBAC final | Confirmar el punto abierto 1 de FUNCTIONAL_SPECIFICATIONS.md |
| Las descargas de documentos son solo para el personal | El estudiante no puede descargar sus propias entregas | Hoy no pueden existir entregas (faltan las tablas); se resuelve en la Fase 4 con comprobación de propiedad |
| La base de desarrollo `gabook` no tiene datos | No se pudo validar el flujo manual en el navegador | `php artisan migrate --seed` y `DemoDataSeeder` (ver README) |
