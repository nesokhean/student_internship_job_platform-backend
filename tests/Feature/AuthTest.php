<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_student(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test Student',
            'email' => 'student@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'student',
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'email' => 'student@test.com',
            'role' => 'student',
            'status' => 'active',
        ]);
    }

    public function test_register_company(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test Company',
            'email' => 'company@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'company',
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);
    }

    public function test_reject_admin_registration(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'admin',
        ]);

        $response->assertStatus(403);
    }

    public function test_login_requires_verification(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password123!'),
            'role' => 'student',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(403);
    }
}
