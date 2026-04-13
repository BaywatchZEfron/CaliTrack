<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExerciseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $exercises = Exercise::where('is_default', true)
            ->orWhere('user_id', $request->user()->id)
            ->orderBy('category')
            ->orderBy('name')
            ->get();
        // Devuelve ejercicios del sistema (is_default=true)
        // MÁS los ejercicios personalizados de este usuario concreto
        // Ordenados por categoría y nombre para que el frontend los reciba ordenados

        return response()->json($exercises);
    }
}