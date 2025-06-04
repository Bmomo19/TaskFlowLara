<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TaskTest extends TestCase
{

    #[Test]
    public function user_can_create_a_task()
    {
        $user = User::factory()->create();

        // Crée un "task" en mémoire, pas en base
        $taskData = Task::factory()->make()->toArray();

        $token = $user->createToken('API Token')->accessToken;

        $response = $this->postJson('/api/tasks', $taskData, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => $taskData['title'],
                'description' => $taskData['description'],
            ]);

        // Optionnel : vérifier que la tâche est bien en base
        $this->assertDatabaseHas('tasks', [
            'title' => $taskData['title'],
            'description' => $taskData['description'],
        ]);
    }

}
