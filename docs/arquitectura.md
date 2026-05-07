# Arquitectura

## Visión general

CaliTrack sigue una arquitectura cliente-servidor desacoplada. El frontend Vue se comunica exclusivamente con el backend Laravel a través de una API REST autenticada con tokens Sanctum.

## Flujo de una petición

1. Usuario interactúa con Vue
2. Composable llama al servicio
3. Axios añade el token Bearer automáticamente (interceptor)
4. Nginx proxy recibe la petición en el puerto `80`
5. Si la ruta es `/api/` → redirige a PHP-FPM (`backend:9000`)
6. Si la ruta es `/` → sirve el frontend estático
7. Laravel valida el token con Sanctum
8. Controller ejecuta la lógica de negocio
9. Eloquent consulta MySQL
10. JSON response
11. Composable actualiza `ref()` reactivo
12. Vue re-renderiza el template automáticamente

## Autenticación

El sistema usa tokens Sanctum (no cookies). Al hacer login:

1. Frontend envía `email + password` a `POST /api/auth/login`
2. Laravel verifica credenciales y devuelve un token
3. El token se guarda en `localStorage` como `auth_token`
4. Cada petición posterior incluye `Authorization: Bearer {token}` automáticamente via interceptor de Axios
5. Si el backend devuelve 401, el interceptor borra el token y redirige a `/login`

## Arquitectura Docker

Puerto `80` (host)
↓
`nginx proxy`
├── `/*` → `frontend:80` (nginx sirviendo Vue compilado)
└── `/api/*` → `backend:9000` (PHP-FPM Laravel)
↓
`mysql:3306`

### Contenedores

| Contenedor | Imagen base | Función |
|-----------|-------------|---------|
| calitrack_nginx | nginx:alpine | Proxy inverso, punto de entrada |
| calitrack_frontend | node:20-alpine + nginx:alpine | Vue compilado, servido estático |
| calitrack_backend | php:8.3-fpm-alpine | API Laravel con PHP-FPM |
| calitrack_mysql | mysql:8.0 | Base de datos con volumen persistente |

## Modelo de datos

### users
- `id`
- `name`
- `email`
- `password`
- `age`
- `height_cm`
- `weight_kg`
- `level` (beginner / intermediate / advanced)
- `goal` (strength / endurance / weight_loss / skill)
- `plan` (free / premium)

### workouts
- Pertenece a `user`
- Relaciones:
  - `workout_exercises` (pertenece a workout)
    - `exercise_id`
    - `load_type`
    - `order_index`
    - `sets` (pertenece a workout_exercise)
      - `set_number`
      - `reps`
      - `weight_kg`
      - `rpe`

### exercises
- Catálogo del sistema

### body_weight_log
- Tabla creada, endpoints pendientes

### sleep_log
- Tabla creada, endpoints pendientes

## API REST — Endpoints

| Método | Ruta | Auth | Descripción |
|--------|------|------|-------------|
| POST | /api/auth/register | No | Registro de usuario |
| POST | /api/auth/login | No | Login, devuelve token |
| POST | /api/auth/logout | Sí | Invalida token |
| GET | /api/user | Sí | Usuario autenticado |
| PUT | /api/user/profile | Sí | Actualiza perfil y plan |
| GET | /api/exercises | Sí | Catálogo de ejercicios |
| GET | /api/workouts | Sí | Historial de entrenamientos |
| POST | /api/workouts | Sí | Crear entrenamiento |
| DELETE | /api/workouts/{id} | Sí | Eliminar entrenamiento |
| GET | /api/dashboard | Sí | KPIs calculados |
| GET | /api/progress | Sí | Progresión por ejercicio |