<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'       => 'sometimes|string|max:255',
            'age'        => 'sometimes|integer|min:10|max:100',
            'height_cm'  => 'sometimes|integer|min:100|max:250',
            'weight_kg'  => 'sometimes|integer|min:30|max:300',
            'level'      => 'sometimes|in:beginner,intermediate,advanced',
            'goal'       => 'sometimes|in:strength,endurance,weight_loss,skill',
            // 'sometimes' → el campo es opcional, solo se valida si viene en la petición
            // Así el frontend puede enviar solo los campos que cambiaron
        ]);

        $request->user()->update($validated);
        // update() con los datos validados — Eloquent solo actualiza
        // los campos que vienen en $validated

        return response()->json($request->user()->fresh());
        // fresh() → recarga el usuario desde la BD
        // Garantiza que la respuesta refleja el estado real guardado
    }
}