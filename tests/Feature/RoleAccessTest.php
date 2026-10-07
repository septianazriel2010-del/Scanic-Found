<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_teacher_and_student_login_with_expected_route_access(): void
    {
        $admin = User::factory()->admin()->create(['email' => 'admin.qa@example.test']);
        $teacher = User::factory()->teacher()->create(['email' => 'teacher.qa@example.test']);
        $student = User::factory()->create(['email' => 'student.qa@example.test']);

        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));

        foreach ([$admin, $teacher, $student] as $user) {
            $this->post(route('login'), [
                'email' => $user->email,
                'password' => 'password',
            ])->assertRedirect($user->isAdmin() ? route('admin.dashboard') : route('items.index'));

            $this->assertAuthenticatedAs($user);
            $this->get(route('items.create'))->assertOk();

            if ($user->isAdmin()) {
                $this->get(route('admin.dashboard'))->assertOk();
                $this->get(route('admin.users.index'))->assertOk();
            } else {
                $this->get(route('admin.dashboard'))->assertForbidden();
                $this->get(route('admin.users.index'))->assertForbidden();
            }

            $this->post(route('logout'))->assertRedirect(route('login'));
        }
    }

    public function test_registration_cannot_assign_admin_role(): void
    {
        $this->post(route('register'), [
            'name' => 'Attempt Admin',
            'email' => 'attempt-admin@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => User::ROLE_ADMIN,
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'attempt-admin@example.test']);
    }
}
