<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_registration_returns_user_and_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0999999999',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonPath('message', 'Registered successfully.')
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.name', 'Test User')
            ->assertJsonPath('data.user.email', 'test@example.com')
            ->assertJsonPath('data.user.phone', '0999999999');

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);

        $user = User::where('email', 'test@example.com')->firstOrFail();

        $this->assertNotSame('password123', $user->password);
        $this->assertTrue(password_verify('password123', $user->password));
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
            'name' => 'mobile_app',
        ]);
    }

    public function test_duplicate_email_returns_validation_error(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Another User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('code', 'VALIDATION_ERROR')
            ->assertJsonValidationErrors(['email']);
    }

    public function test_weak_password_returns_validation_error(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Weak Password User',
            'email' => 'weak@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('code', 'VALIDATION_ERROR')
            ->assertJsonValidationErrors(['password']);
    }

    public function test_missing_fields_return_validation_error(): void
    {
        $response = $this->postJson('/api/v1/auth/register', []);

        $response
            ->assertStatus(422)
            ->assertJsonPath('code', 'VALIDATION_ERROR')
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }
}
