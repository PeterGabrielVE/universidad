# Pruebas

## Requisitos

Las migraciones usan SQL específico de MySQL (`ALTER TABLE ... MODIFY`), así que
las pruebas corren contra MySQL en una base **separada**, `gabook_testing`.
La base de desarrollo `gabook` nunca se toca: `phpunit.xml` fuerza `DB_DATABASE` en `$_SERVER` y `$_ENV`
(el contenedor define `DB_DATABASE=gabook`) y `Tests\TestCase` aborta si la base no termina en `_testing`.

Primera vez:

```bash
docker compose up -d db app
docker compose exec db mysql -uroot -prootpassword \
  -e "CREATE DATABASE IF NOT EXISTS gabook_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

## Ejecutar

Dentro del contenedor (PHP 8.1, igual que el entorno de ejecución) y **como `www-data`**: las pruebas
compilan vistas en `storage/`, y si las escribe `root`, Apache falla después con *Permission denied*.

```bash
docker compose exec -u www-data -e HOME=/tmp app php vendor/bin/phpunit                 # todo
docker compose exec -u www-data -e HOME=/tmp app php vendor/bin/phpunit --testsuite Feature
docker compose exec -u www-data -e HOME=/tmp app php vendor/bin/phpunit --filter Security
```

Cada prueba usa `RefreshDatabase`: migra una vez y revierte cada prueba en una transacción.

## Estructura

| Carpeta | Contenido |
|---|---|
| `tests/Unit/<Módulo>/Domain` | Reglas de dominio en PHP puro (matriz RBAC, alcance por programa, correo, estados) |
| `tests/Unit/<Módulo>/Application` | Casos de uso con adaptadores en memoria: sin Laravel ni base de datos |
| `tests/Support/<Módulo>` | Dobles de prueba de los puertos (`InMemoryUserRepository`, `SpyStudentRecords`…) |
| `tests/Feature/Security` | Autenticación, autorización por permiso, alcance, subidas, API |
| `tests/Feature/Characterization` | Comportamiento existente que los refactors deben conservar |
| `tests/Feature/IdentityAccess` | Registro de Aspirantes y ciclo de vida de la cuenta |
| `tests/Concerns/CreatesUsers.php` | Usuarios con rol de Spatie y estudiantes con expediente (sobre las factories) |

Las pruebas unitarias se ejecutan solas y en milisegundos:

```bash
docker compose exec -u www-data -e HOME=/tmp app php vendor/bin/phpunit --testsuite Unit
```

## Convenciones

- Una prueba por regla o comportamiento; el nombre describe el comportamiento esperado.
- Dobles de prueba solo en los límites (almacenamiento con `Storage::fake`, correo, tiempo).
  En los casos de uso se preferirán repositorios en memoria a los mocks.
- Todo hallazgo de seguridad o bug corregido lleva su prueba de regresión.

## Compilar el frontend

El `node_modules` local puede no coincidir con `package-lock.json`. Para compilar en
un entorno limpio sin tocar el `node_modules` del host:

```bash
docker run --rm -v "$PWD:/app" -v /app/node_modules -w /app node:18 \
  sh -c "npm ci && npm run development"
```
