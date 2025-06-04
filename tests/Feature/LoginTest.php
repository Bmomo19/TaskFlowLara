<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;

class LoginTest extends TestCase
{
    #[Test]
    public function user_can_login_with_valid_credentials()
    {
        // Arrange
        $password = 'password123'; // On garde le password connu pour le test
        $user = User::factory()->create([
            'password' => bcrypt($password),
        ]);

        // Act
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'token'
            ]);
    }

    #[Test]
    public function login_fails_with_wrong_password()
    {
        // Arrange
        $user = User::factory()->create([
            'password' => bcrypt('correctpassword'),
        ]);

        // Act
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        // Assert
        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized'
            ]);
    }

    #[Test]
    public function login_fails_with_invalid_email()
    {
        // Act
        $response = $this->postJson('/api/login', [
            'email' => 'notanemail',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function login_fails_when_fields_are_missing()
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }
}
