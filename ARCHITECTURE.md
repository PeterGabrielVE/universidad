# Arquitectura

## Visión general

Gabook es un monolito Laravel 10 que evoluciona hacia una **arquitectura hexagonal
modular** (Ports & Adapters) sin reescritura: cada módulo se extrae cuando se trabaja
en él (ver IMPLEMENTATION_PLAN.md). Hoy conviven dos estilos:

| Zona | Estilo | Módulos |
|---|---|---|
| `app/Modules/*` | Hexagonal: dominio puro, casos de uso, puertos y adaptadores | IdentityAccess (Fase 2) |
| `app/Http/Controllers`, `app/*.php` | MVC heredado: lógica en controladores y modelos Eloquent | Estudiantes, lapsos, entregas, reportes (Fases 3–5) |

## Capas y regla de dependencias

```
            ┌──────────────────────────────────────────────┐
            │  Presentación (app/Http, routes, views, Vue) │
            │  Controladores, FormRequests, middleware     │
            └───────────────┬──────────────────────────────┘
                            │ llama a casos de uso
            ┌───────────────▼──────────────────────────────┐
            │  Aplicación (Modules/*/Application)          │
            │  Casos de uso, DTO, puertos (interfaces)     │
            └───────────────┬──────────────────────────────┘
                            │ usa
            ┌───────────────▼──────────────────────────────┐
            │  Dominio (Modules/*/Domain)                  │
            │  Entidades, value objects, enums, reglas,    │
            │  excepciones. Sin Laravel, sin base de datos │
            └──────────────────────────────────────────────┘
                            ▲ implementa los puertos
            ┌───────────────┴──────────────────────────────┐
            │  Infraestructura (Modules/*/Infrastructure)  │
            │  Eloquent, Spatie, Hash, transacciones,      │
            │  ServiceProvider con los bindings            │
            └──────────────────────────────────────────────┘
```

- El **dominio** no importa nada de Laravel ni de la base de datos.
- La **aplicación** depende de puertos (interfaces), nunca de adaptadores concretos.
- La **infraestructura** implementa los puertos y se conecta en el ServiceProvider del módulo.
- La **presentación** valida la forma de la entrada (FormRequest), delega en el caso de
  uso y traduce el resultado a HTTP. No contiene reglas de negocio.

## Módulo IdentityAccess

```
app/Modules/IdentityAccess/
├── Domain/
│   ├── UserAccount.php          Entidad: identidad, rol, estado, programa
│   ├── Email.php                VO: correo normalizado (unicidad sin importar mayúsculas)
│   ├── AccountStatus.php        Enum: activo | inactivo | suspendido
│   ├── RoleName.php             Enum: los 7 roles del sistema
│   ├── Permission.php           Enum: permisos de las funcionalidades existentes
│   ├── AccessPolicy.php         Matriz RBAC: permisos de cada rol (única fuente de verdad)
│   ├── ProgramScope.php         Alcance por programa del Coordinador (3 = ambos)
│   └── Exception/               Errores de negocio (con el campo de formulario afectado)
├── Application/
│   ├── Port/                    UserRepository, RoleRepository, PasswordHasher,
│   │                            StudentRecords, TransactionManager
│   ├── Dto/                     UserData, RoleData
│   └── UseCase/                 CreateUser, UpdateUser, DeleteUser,
│                                RegisterApplicant, ManageRoles
└── Infrastructure/
    ├── EloquentUserRepository   Tabla users + rol en Spatie
    ├── SpatieRoleRepository     Roles sobre el modelo de Spatie
    ├── UserDirectory            Lado de lectura: listados, filtros, conteos, alcance
    ├── LaravelPasswordHasher, EloquentStudentRecords, DatabaseTransactionManager
    └── IdentityAccessServiceProvider   Bindings puerto → adaptador
```

### Flujo: alta de un usuario

```
POST /api/v1/users  (o POST /user desde Blade)
  → middleware auth:sanctum, active, permission:users.manage
  → UserRequest           valida la forma y construye UserData
  → CreateUser::execute
       Email::from()      normaliza y valida el correo
       UserRepository     ¿correo en uso?            → EmailAlreadyInUse (422, campo email)
       RoleRepository     ¿existe el rol?            → RoleNotFound (422, campo role)
       PasswordHasher     hash de la contraseña
       TransactionManager guarda la cuenta y, si es Estudiante, crea su expediente
  → UserAccountPresenter  respuesta 201
```

### Autorización

1. **Rutas por permiso**, no por nombre de rol: `permission:users.manage`.
2. **Qué rol tiene cada permiso** lo define `AccessPolicy` (dominio, con pruebas
   unitarias). `RolesTableSeeder` lo vuelca en Spatie; se vuelve a ejecutar tras
   cambiar la matriz.
3. **Alcance por programa:** el middleware `student.access` comprueba que el
   expediente pertenezca al estudiante autenticado o esté en el `ProgramScope` del
   personal. El listado de estudiantes aplica el mismo alcance.
4. **Estado de cuenta:** `LoginController` rechaza cuentas no activas y el middleware
   `active` cierra la sesión si la cuenta se suspende estando conectada.

### Errores

`App\Modules\Shared\Domain\DomainException` (regla de negocio) se traduce en
`app/Exceptions/Handler.php` a 422 (JSON con `errors.<campo>`, o vuelta al formulario
con el error en su campo). `NotFoundException` se traduce a 404. No se registran en el
log porque son errores esperados.

## Decisiones

| Decisión | Motivo |
|---|---|
| Hexagonal solo donde hay reglas de negocio | Catálogos (países, sedes) siguen con Eloquent directo: abstraerlos no aporta |
| `ManageRoles` agrupa las operaciones de roles | Reemplaza `RoleService` + 5 UseCases que solo reenviaban llamadas |
| `UserDirectory` sin puerto | Es un lado de lectura sin reglas; un puerto sería una abstracción sin uso alternativo |
| Un solo modelo `App\Models\User` | `App\User` y `App\Models\User` coexistían sobre la misma tabla con `$fillable` distintos |
| Roles solo en Spatie | `users.rol_id` duplicaba el dato y se desincronizaba; la columna queda sin uso |
| Matriz RBAC en código | Una sola fuente versionada y probada; Spatie es el mecanismo, no la definición |
| Roles del sistema protegidos | Renombrar o borrar uno rompería la matriz de permisos |
| Estados como *backed enums* sobre `string` | MySQL ENUM exige `ALTER TABLE` para cada valor nuevo |
| `password` con cast `hashed` | No vuelve a hashear un hash (el mutador anterior sí lo hacía) |

## Pendiente de migrar a módulos

`EstudiantesController` (≈1.200 líneas: expediente, entregas, calificaciones, PDFs) y
`LapsoController` se extraen en las Fases 3–5 (Academic, DoctoralSubmissions, Reporting).
