<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Models\Task;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('tasks', TaskController::class);

});

Route::get('/task/count', function () {
    return ['total' => Task::count()];
});


Route::get('/test-broadcast', function () {
    broadcast(new \App\Events\TaskCreated(10)); // 👈 exemple avec 10 tâches
    return 'Event broadcasted';
});
