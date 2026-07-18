# API Registry Points

API REST desarrollada en Laravel para la gestión de puntos escolares: colegios, cursos, maestros, materias, estudiantes, categorías de puntos y registro de puntos.

## Requisitos

- PHP >= 8.2
- Composer
- Node.js y npm
- MySQL

## Instalación

```bash
composer install
cp .env.example .env
php artisan key:generate

Configura la conexión a MySQL en el archivo .env:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_base_datos
DB_USERNAME=usuario
DB_PASSWORD=contraseña

Luego ejecuta las migraciones:

php artisan migrate
npm install
npm run build

Levantar el servidor de desarrollo:

php artisan serve

Autenticación

La API usa Laravel Sanctum con abilities (permisos por token) para proteger cada grupo de rutas.

- Login: POST /api/auth/login
- Crear administrador inicial: GET /api/auth/create-admin

Cada solicitud a rutas protegidas debe incluir el token en el header:

Authorization: Bearer {token}

Endpoints principales

┌──────────────────────────┬──────────────────────────────┬──────────────────────────────────────────────────────┐
│         Recurso          │           Prefijo            │                     Descripción                      │
├──────────────────────────┼──────────────────────────────┼──────────────────────────────────────────────────────┤
│ Colegios                 │ /api/schools                 │ CRUD de colegios                                     │
├──────────────────────────┼──────────────────────────────┼──────────────────────────────────────────────────────┤
│ Cursos                   │ /api/courses                 │ CRUD de cursos/grados                                │
├──────────────────────────┼──────────────────────────────┼──────────────────────────────────────────────────────┤
│ Maestros                 │ /api/teachers                │ CRUD de maestros                                     │
├──────────────────────────┼──────────────────────────────┼──────────────────────────────────────────────────────┤
│ Materias                 │ /api/subjects                │ CRUD de materias                                     │
├──────────────────────────┼──────────────────────────────┼──────────────────────────────────────────────────────┤
│ Estudiantes              │ /api/students                │ CRUD de estudiantes                                  │
├──────────────────────────┼──────────────────────────────┼──────────────────────────────────────────────────────┤
│ Categorías de puntos     │ /api/point-categories        │ CRUD de categorías de puntos                         │
├──────────────────────────┼──────────────────────────────┼──────────────────────────────────────────────────────┤
│ Asignación de categorías │ /api/point-category-contexts │ Asignación de categorías de puntos a cursos/materias │
├──────────────────────────┼──────────────────────────────┼──────────────────────────────────────────────────────┤
│ Asignación de materias   │ /api/teachers-subjects       │ Asignación de materias a maestros                    │
├──────────────────────────┼──────────────────────────────┼──────────────────────────────────────────────────────┤
│ Registro de puntos       │ /api/registry-points         │ Registro de puntos por estudiante                    │
└──────────────────────────┴──────────────────────────────┴──────────────────────────────────────────────────────┘

Stack

- Laravel 12
- Laravel Sanctum