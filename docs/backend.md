# Backend — Laravel 11 + Sanctum

CaliTrack usa **Laravel 11** como API REST. El backend expone endpoints JSON que el frontend consume vía Axios. La autenticación se gestiona con **Laravel Sanctum** (tokens por portador). La base de datos es **MySQL 8.0**.

---

## Stack y versiones

| Tecnología | Versión | Rol |
|---|---|---|
| PHP | 8.3 | Lenguaje del servidor |
| Laravel | 11.x | Framework API REST |
| Laravel Sanctum | 4.x | Autenticación por token |
| Eloquent ORM | (incluido en Laravel) | Consultas a la base de datos |
| MySQL | 8.0 | Base de datos relacional |
| Carbon | (incluido en Laravel) | Manipulación de fechas |

---

## Estructura de directorios relevante

```
backend/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AuthController.php
│   │       ├── ExerciseController.php
│   │       ├── WorkoutController.php
│   │       ├── ProfileController.php
│   │       ├── DashboardController.php
│   │       └── ProgressController.php
│   └── Models/
│       ├── User.php
│       ├── Exercise.php
│       ├── Workout.php
│       ├── WorkoutExercise.php
│       └── Set.php
├── database/
│   ├── migrations/          ← Define la estructura de cada tabla
│   └── seeders/
│       ├── ExerciseSeeder.php   ← 20 ejercicios de calistenia
│       └── DemoUserSeeder.php   ← Usuario demo con 12 semanas de datos
├── routes/
│   └── api.php              ← Todos los endpoints de la API
├── config/
│   └── sanctum.php          ← Configuración de Sanctum
└── .env                     ← Conexión a BD, APP_KEY, etc.
```

---

## Base de datos — Modelo de datos

Las tablas siguen las relaciones de un diario de entrenamiento real:

```
users
 └── workouts           (una sesión por día)
      └── workout_exercises  (ejercicios dentro de esa sesión)
           └── sets           (series concretas de cada ejercicio)

exercises              (catálogo global, no pertenece a ningún user)
```

### Tabla `users`

```
id              INT         PK autoincrement
name            VARCHAR
email           VARCHAR     único
password        VARCHAR     hash bcrypt
age             INT         nullable
height_cm       INT         nullable
weight_kg       DECIMAL     nullable
level           ENUM        beginner / intermediate / advanced
goal            ENUM        strength / endurance / weight_loss / skill
plan            ENUM        free / premium
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### Tabla `exercises`

```
id              INT         PK
name            VARCHAR     ej: "Pull-up", "Dip", "Planche lean"
muscle_group    ENUM        chest / back / shoulders / arms / legs / core / full_body
category        ENUM        push / pull / legs / core / full_body
is_default      BOOLEAN     true = ejercicio del sistema (creado por seeder)
```

> `load_type` **no está en esta tabla**. Está en `workout_exercises` porque el mismo ejercicio (Pull-up) puede hacerse con peso añadido un día y con banda asistida otro.

### Tabla `workouts`

```
id                  INT         PK
user_id             INT         FK → users.id
date                DATE        ej: "2024-03-15"
duration_minutes    INT         nullable
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

### Tabla `workout_exercises`

```
id              INT         PK
workout_id      INT         FK → workouts.id
exercise_id     INT         FK → exercises.id
load_type       ENUM        bodyweight / weighted / assisted
order_index     INT         orden del ejercicio en la sesión
rest_time       INT         segundos de descanso, nullable
notes           TEXT        nullable
```

### Tabla `sets`

```
id                  INT         PK
workout_exercise_id INT         FK → workout_exercises.id
set_number          INT         orden de la serie: 1, 2, 3...
reps                INT
weight_kg           DECIMAL(5,2) 0.00 si es bodyweight
is_assistance       BOOLEAN     true si usó banda elástica
rpe                 INT         0-10, esfuerzo percibido
```

### Tablas creadas sin endpoints (fuera del MVP)

```
body_weight_log   (user_id, date, weight_kg)
sleep_log         (user_id, date, hours, quality)
```

---

## Modelos Eloquent

Eloquent mapea cada tabla a una clase PHP. Define las relaciones y los campos que se pueden asignar en masa (`fillable`).

### `User.php`

```php
class User extends Authenticatable
{
    use HasApiTokens;  // ← necesario para Sanctum: genera y valida tokens

    // Campos que se pueden asignar con User::create([...]) o $user->update([...])
    // Si un campo no está aquí, Eloquent lanza excepción al intentar asignarlo
    protected $fillable = [
        'name', 'email', 'password',
        'age', 'height_cm', 'weight_kg',
        'level', 'goal', 'plan',
    ];

    // Campos que nunca se incluyen en respuestas JSON
    protected $hidden = ['password', 'remember_token'];

    // Un usuario tiene muchos workouts
    public function workouts()
    {
        return $this->hasMany(Workout::class);
    }
}
```

### `Workout.php`

```php
class Workout extends Model
{
    protected $fillable = ['user_id', 'date', 'duration_minutes'];

    // Un workout tiene muchos workout_exercises
    // with(['workoutExercises.sets', 'workoutExercises.exercise'])
    // carga todo en una sola query (eager loading)
    public function workoutExercises()
    {
        return $this->hasMany(WorkoutExercise::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### `WorkoutExercise.php`

```php
class WorkoutExercise extends Model
{
    protected $fillable = [
        'workout_id', 'exercise_id', 'load_type',
        'order_index', 'rest_time', 'notes',
    ];

    public function sets()
    {
        return $this->hasMany(Set::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}
```

### `Set.php`

```php
class Set extends Model
{
    protected $fillable = [
        'workout_exercise_id', 'set_number',
        'reps', 'weight_kg', 'is_assistance', 'rpe',
    ];

    public function workoutExercise()
    {
        return $this->belongsTo(WorkoutExercise::class);
    }
}
```

---

## Rutas — `routes/api.php`

```php
// Rutas públicas: no requieren token
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login',    [AuthController::class, 'login']);

// Rutas protegidas: Sanctum verifica el token en cada petición
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout',    [AuthController::class, 'logout']);
    Route::get('/user',            [AuthController::class, 'user']);
    Route::put('/user/profile',    [ProfileController::class, 'update']);

    Route::get('/exercises',       [ExerciseController::class, 'index']);

    Route::get('/workouts',        [WorkoutController::class, 'index']);
    Route::post('/workouts',       [WorkoutController::class, 'store']);
    Route::get('/workouts/{id}',   [WorkoutController::class, 'show']);
    Route::delete('/workouts/{id}',[WorkoutController::class, 'destroy']);

    Route::get('/dashboard',       [DashboardController::class, 'index']);
    Route::get('/progress',        [ProgressController::class, 'index']);
});
```

El middleware `auth:sanctum` extrae el token del header `Authorization: Bearer {token}`, lo valida contra la tabla `personal_access_tokens` y, si es válido, inyecta el usuario en `$request->user()`. Si el token no existe o está revocado, devuelve `401`.

---

## Controladores

### `AuthController.php`

```php
// POST /api/auth/register
public function register(Request $request)
{
    // Valida los campos obligatorios antes de tocar la BD
    $validated = $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users',  // unique evita duplicados
        'password' => 'required|min:8',
    ]);

    // Crea el usuario. bcrypt() hashea la contraseña antes de guardarla
    $user = User::create([
        ...$validated,
        'password' => bcrypt($validated['password']),
        'plan'     => 'free',  // plan por defecto al registrarse
    ]);

    // Genera un token de Sanctum asociado a este usuario
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json(['token' => $token, 'user' => $user], 201);
}

// POST /api/auth/login
public function login(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    // Busca el usuario por email y verifica la contraseña con Hash::check()
    $user = User::where('email', $request->email)->firstOrFail();

    if (!Hash::check($request->password, $user->password)) {
        // 401 si la contraseña no coincide
        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }

    // Revoca tokens anteriores para que solo haya uno activo por usuario
    $user->tokens()->delete();
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json(['token' => $token, 'user' => $user]);
}

// POST /api/auth/logout
public function logout(Request $request)
{
    // Revoca el token actual (el que vino en el header de esta petición)
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Sesión cerrada']);
}

// GET /api/user
public function user(Request $request)
{
    // Sanctum ya resolvió el usuario a partir del token
    return response()->json($request->user());
}
```

### `WorkoutController.php`

```php
// GET /api/workouts
// Devuelve los workouts del usuario autenticado, con ejercicios y series anidados
public function index(Request $request)
{
    $workouts = Workout::where('user_id', $request->user()->id)
        ->with(['workoutExercises.sets', 'workoutExercises.exercise'])
        // with() hace eager loading: carga todo en 3 queries en vez de N+1
        ->orderBy('date', 'desc')
        ->get();

    return response()->json($workouts);
}

// POST /api/workouts
// Recibe el workout completo (con ejercicios y series anidados) y lo persiste
public function store(Request $request)
{
    $validated = $request->validate([
        'date'                              => 'required|date',
        'duration_minutes'                  => 'nullable|integer',
        'workout_exercises'                 => 'required|array',
        'workout_exercises.*.exercise_id'   => 'required|exists:exercises,id',
        'workout_exercises.*.load_type'     => 'required|in:bodyweight,weighted,assisted',
        'workout_exercises.*.sets'          => 'required|array',
        'workout_exercises.*.sets.*.reps'   => 'required|integer|min:0',
        'workout_exercises.*.sets.*.rpe'    => 'required|integer|min:0|max:10',
    ]);

    // Crea el workout padre
    $workout = Workout::create([
        'user_id'          => $request->user()->id,
        'date'             => $validated['date'],
        'duration_minutes' => $validated['duration_minutes'] ?? null,
    ]);

    // Para cada ejercicio en el array, crea su registro y sus series
    foreach ($validated['workout_exercises'] as $index => $weData) {
        $we = $workout->workoutExercises()->create([
            'exercise_id' => $weData['exercise_id'],
            'load_type'   => $weData['load_type'],
            'order_index' => $index + 1,
            'rest_time'   => $weData['rest_time'] ?? null,
            'notes'       => $weData['notes'] ?? null,
        ]);

        // Crea las series de este ejercicio
        foreach ($weData['sets'] as $setIndex => $setData) {
            $we->sets()->create([
                'set_number'    => $setIndex + 1,
                'reps'          => $setData['reps'],
                'weight_kg'     => $setData['weight_kg'] ?? 0,
                'is_assistance' => $setData['is_assistance'] ?? false,
                'rpe'           => $setData['rpe'],
            ]);
        }
    }

    // Devuelve el workout creado con todas las relaciones cargadas
    return response()->json(
        $workout->load(['workoutExercises.sets', 'workoutExercises.exercise']),
        201
    );
}
```

### `DashboardController.php`

Este es el controlador más complejo. Calcula todos los KPIs del dashboard a partir del historial real del usuario.

```php
public function index(Request $request)
{
    $user = $request->user();
    $now  = Carbon::now();

    // --- KPI 1: Entrenamientos este mes ---
    $workoutsThisMonth = Workout::where('user_id', $user->id)
        ->whereMonth('date', $now->month)
        ->whereYear('date', $now->year)
        ->count();

    // --- KPI 2: Semanas activas (últimas 12 semanas) ---
    // Cuenta semanas distintas en las que hubo al menos un workout
    $activeWeeks = Workout::where('user_id', $user->id)
        ->where('date', '>=', $now->copy()->subWeeks(12))
        ->selectRaw('YEARWEEK(date, 1) as week')  // ISO week
        ->distinct()
        ->count();

    // --- KPI 3: Ejercicio más frecuente ---
    // Agrupa por exercise_id, cuenta cuántas veces aparece en workouts del usuario
    $mostFrequent = WorkoutExercise::whereHas('workout', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->select('exercise_id', DB::raw('COUNT(*) as total'))
        ->groupBy('exercise_id')
        ->orderByDesc('total')
        ->with('exercise')
        ->first();

    // --- KPI 4: Performance score (0-100) ---
    // Fórmula simple: (semanas activas / 12) * 100, máximo 100
    $score = min(100, round(($activeWeeks / 12) * 100));

    // --- Insights automáticos ---
    $insights = [];

    // Si lleva más de 7 días sin entrenar, avisa
    $lastWorkout = Workout::where('user_id', $user->id)
        ->orderByDesc('date')->first();

    if ($lastWorkout && Carbon::parse($lastWorkout->date)->diffInDays($now) > 7) {
        $insights[] = [
            'type'    => 'warning',
            'message' => 'Llevas más de 7 días sin registrar un entrenamiento.',
        ];
    }

    if ($activeWeeks >= 8) {
        $insights[] = [
            'type'    => 'positive',
            'message' => "¡{$activeWeeks} semanas activas! Consistencia excelente.",
        ];
    }

    return response()->json([
        'workouts_this_month'   => $workoutsThisMonth,
        'active_weeks'          => $activeWeeks,
        'performance_score'     => $score,
        'most_frequent_exercise'=> $mostFrequent?->exercise?->name,
        'insights'              => $insights,
    ]);
}
```

### `ProgressController.php`

Agrupa las series por semana para construir la gráfica de evolución.

```php
public function index(Request $request)
{
    $user = $request->user();

    // Obtiene todos los ejercicios distintos que ha usado el usuario
    $exercises = Exercise::whereHas('workoutExercises.workout', function ($q) use ($user) {
        $q->where('user_id', $user->id);
    })->get();

    $result = [];

    foreach ($exercises as $exercise) {
        // Para cada ejercicio, agrupa las series por semana ISO
        // Calcula: promedio de reps, peso máximo y volumen total por semana
        $weeklyData = Set::whereHas('workoutExercise', function ($q) use ($user, $exercise) {
                $q->where('exercise_id', $exercise->id)
                  ->whereHas('workout', fn($q2) => $q2->where('user_id', $user->id));
            })
            ->join('workout_exercises', 'sets.workout_exercise_id', '=', 'workout_exercises.id')
            ->join('workouts', 'workout_exercises.workout_id', '=', 'workouts.id')
            ->selectRaw('
                YEARWEEK(workouts.date, 1) as week,
                AVG(sets.reps)              as avg_reps,
                MAX(sets.weight_kg)         as max_weight,
                SUM(sets.reps * sets.weight_kg) as total_volume
            ')
            ->groupBy('week')
            ->orderBy('week')
            ->get();

        $result[] = [
            'exercise' => $exercise,
            'data'     => $weeklyData,
        ];
    }

    return response()->json($result);
}
```

---

## Autenticación con Sanctum

Sanctum usa la tabla `personal_access_tokens` para guardar cada token generado:

```
personal_access_tokens
├── tokenable_type   "App\Models\User"
├── tokenable_id     1   (id del usuario)
├── name             "auth_token"
├── token            hash SHA-256 del token
└── last_used_at     actualizado en cada petición
```

Cuando el frontend envía `Authorization: Bearer 1|abc123...`, Laravel:

1. Separa el ID del token (`1`) del valor (`abc123...`)
2. Busca el registro con ese ID en `personal_access_tokens`
3. Compara el hash SHA-256 de `abc123...` con el campo `token`
4. Si coincide, carga el usuario vinculado y lo inyecta en `$request->user()`

---

## Seeders

### `ExerciseSeeder.php`

Crea los 20 ejercicios del catálogo. Se ejecuta una sola vez.

```php
public function run()
{
    $exercises = [
        ['name' => 'Pull-up',       'muscle_group' => 'back',      'category' => 'pull', 'is_default' => true],
        ['name' => 'Dip',           'muscle_group' => 'chest',     'category' => 'push', 'is_default' => true],
        ['name' => 'Push-up',       'muscle_group' => 'chest',     'category' => 'push', 'is_default' => true],
        ['name' => 'Muscle-up',     'muscle_group' => 'full_body', 'category' => 'pull', 'is_default' => true],
        ['name' => 'Planche lean',  'muscle_group' => 'shoulders', 'category' => 'push', 'is_default' => true],
        // ... 15 más
    ];

    foreach ($exercises as $exercise) {
        Exercise::create($exercise);
    }
}
```

### `DemoUserSeeder.php`

Crea el usuario demo con 12 semanas de datos realistas. Útil para demostrar el dashboard y la progresión sin necesidad de introducir datos manualmente.

```php
public function run()
{
    // Crea o actualiza el usuario demo
    $user = User::updateOrCreate(
        ['email' => 'demo@calitrack.com'],
        [
            'name'       => 'Demo User',
            'password'   => bcrypt('demo1234'),
            'age'        => 25,
            'height_cm'  => 178,
            'weight_kg'  => 75.5,
            'level'      => 'intermediate',
            'goal'       => 'strength',
            'plan'       => 'premium',
        ]
    );

    // Genera workouts para las últimas 12 semanas
    // 3-4 entrenamientos por semana con progresión realista en reps
    for ($week = 12; $week >= 0; $week--) {
        $date = Carbon::now()->subWeeks($week)->startOfWeek();

        // Lunes, miércoles, viernes
        foreach ([0, 2, 4] as $dayOffset) {
            $workout = Workout::create([
                'user_id'          => $user->id,
                'date'             => $date->copy()->addDays($dayOffset),
                'duration_minutes' => rand(40, 75),
            ]);

            // Pull-up: empieza en 6 reps, sube ~0.5 por semana
            $we = $workout->workoutExercises()->create([
                'exercise_id' => 1,  // Pull-up
                'load_type'   => 'bodyweight',
                'order_index' => 1,
            ]);

            $baseReps = 6 + floor((12 - $week) * 0.5);
            for ($set = 1; $set <= 4; $set++) {
                $we->sets()->create([
                    'set_number'    => $set,
                    'reps'          => $baseReps - ($set - 1),  // baja 1 rep por serie
                    'weight_kg'     => 0,
                    'is_assistance' => false,
                    'rpe'           => rand(7, 9),
                ]);
            }
        }
    }
}
```

**Ejecutar seeders en Docker:**

```bash
# Ejecutar todas las migraciones
docker exec -it calitrack_backend php artisan migrate --force

# Ejecutar todos los seeders
docker exec -it calitrack_backend php artisan db:seed --force

# Solo el usuario demo (si ya tienes los ejercicios)
docker exec -it calitrack_backend php artisan db:seed --class=DemoUserSeeder --force
```

---

## Migraciones

Las migraciones definen la estructura de la BD y se ejecutan en orden. Laravel registra cuáles se han ejecutado en la tabla `migrations`.

```bash
# Ver estado de las migraciones
docker exec -it calitrack_backend php artisan migrate:status

# Ejecutar migraciones pendientes
docker exec -it calitrack_backend php artisan migrate --force

# Rollback de la última migración (¡borra datos!)
docker exec -it calitrack_backend php artisan migrate:rollback
```

Ejemplo de migración para la columna `plan` en `users`:

```php
// database/migrations/xxxx_add_plan_to_users_table.php
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // ENUM con valor por defecto 'free'
        // Si se añade después de crear la tabla, va después de 'goal'
        $table->enum('plan', ['free', 'premium'])->default('free')->after('goal');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('plan');
    });
}
```

---

## `.env` del backend

```ini
APP_NAME=CaliTrack
APP_ENV=local
APP_KEY=base64:...          # generado con php artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost

# Conexión a MySQL dentro de Docker
DB_CONNECTION=mysql
DB_HOST=mysql               # nombre del servicio en docker-compose.yml
DB_PORT=3306
DB_DATABASE=calitrack
DB_USERNAME=calitrack_user
DB_PASSWORD=secret

# Sanctum: dominios de frontend autorizados
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:5173
```

> En desarrollo con Herd, `DB_HOST=127.0.0.1` y `DB_PORT=3306`.

---

## Comandos frecuentes

```bash
# Limpiar caché de configuración (necesario si cambias .env)
docker exec -it calitrack_backend php artisan config:clear

# Ver todas las rutas registradas
docker exec -it calitrack_backend php artisan route:list

# Crear un controlador nuevo
docker exec -it calitrack_backend php artisan make:controller NombreController

# Crear un modelo con migración
docker exec -it calitrack_backend php artisan make:model NombreModelo -m

# Acceder a Tinker (consola interactiva Eloquent)
docker exec -it calitrack_backend php artisan tinker
# Ejemplo dentro de Tinker:
# >>> App\Models\User::where('email', 'demo@calitrack.com')->first()
```

---

## Decisiones técnicas

| Decisión | Alternativa descartada | Motivo |
|---|---|---|
| Sanctum en vez de Passport | Laravel Passport | Sanctum es suficiente para SPA con tokens. Passport añade complejidad (OAuth2) innecesaria para este caso |
| Eager loading con `with()` | Lazy loading por defecto | Sin `with()`, Eloquent haría una query por cada workout (problema N+1). Con `with()`, son 3 queries fijas |
| Validación en el controlador | Form Requests | Para un proyecto de este tamaño, validar directamente en el método es suficiente y más legible |
| `YEARWEEK(date, 1)` para agrupar | Tratar fechas en PHP | La agrupación en SQL es más eficiente: MySQL agrupa y suma antes de devolver datos |
| `updateOrCreate` en DemoUserSeeder | Borrar y recrear | Permite re-ejecutar el seeder sin error de email duplicado |