<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    // Récupérer toutes les tâches de l'utilisateur connecté
    public function index()
    {
        return Auth::user()->tasks; // Récupère toutes ses tâches
    }

    // Créer une nouvelle tâche
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $task = Auth::user()->tasks()->create($request->all());
        return response()->json($task, 201);
    }

    // Afficher une tâche spécifique
    public function show(Task $task): JsonResponse
    {
        return response()->json($task);
    }

    // Mettre à jour une tâche
    public function update(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task); // Vérifie si l'utilisateur peut modifier
        $task->update($request->all());
        return response()->json($task);
    }

    // Supprimer une tâche
    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);
        $task->delete();
        return response()->json(['message' => 'Task deleted']);
    }
}
