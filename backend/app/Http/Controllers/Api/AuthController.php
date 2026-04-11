<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            // 'confirmed' → busca un campo 'password_confirmation' en la petición
            // y verifica que los dos coincidan
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            // Hash::make() → hashea la contraseña antes de guardarla
            // Nunca se guarda en texto plano
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        // createToken() → genera un token en personal_access_tokens
        // plainTextToken → devuelve el token en texto plano (solo esta vez)
        // El frontend debe guardarlo porque no se puede recuperar después

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ], 201);
        // 201 = Created — código HTTP estándar para recursos creados
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            // Hash::check() → compara el password en texto plano
            // con el hash guardado en BD. Nunca comparas en texto plano.
            throw ValidationException::withMessages([
                'email' => ['Las credenciales no son correctas.'],
            ]);
            // Lanza un error 422 con el mensaje — Laravel lo formatea solo
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json($request->user());
        // $request->user() → Sanctum lee el token del header,
        // busca al usuario en BD y lo inyecta aquí automáticamente
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        // Borra solo el token actual, no todos los del usuario
        // Si el usuario tiene la app en móvil y web, solo cierra una sesión

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }
}