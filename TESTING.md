# Pruebas

## Requisitos

Las migraciones usan SQL específico de MySQL (`ALTER TABLE ... MODIFY`), así que
las pruebas corren contra MySQL en una base **separada**, `gabook_testing`.
La base de desarrollo `gabook` nunca se toca: `phpunit.xml` fuerza `DB_DATABASE`.

Primera vez:

```bash
docker compose up -d db app
docker compose exec db mysql -uroot -prootpassword \
  -e "CREATE DATABASE IF NOT EXISTS gabook_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

## Ejecutar

Dentro del contenedor (PHP 8.1, igual que el entorno de ejecución):

```bash
docker compose exec app php vendor/bin/phpunit                 # todo
docker compose exec app php vendor/bin/phpunit --testsuite Feature
docker compose exec app php vendor/bin/phpunit --filter Security
```

Cada prueba usa `RefreshDatabase`: migra una vez y revierte cada prueba en una transacción.

## Estructura

| Carpeta | Contenido |
|---|---|
| `tests/Unit` | Pruebas sin framework (dominio y casos de uso, a partir de la Fase 2) |
| `tests/Feature/Security` | Autenticación, autorización por rol, acceso a expedientes ajenos, subidas, API |
| `tests/Concerns/CreatesUsers.php` | Crea usuarios con `rol_id` y rol de Spatie coherentes, y estudiantes con expediente |

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
