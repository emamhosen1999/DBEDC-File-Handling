<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_for_authenticated_user(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard');
    }

    public function test_guest_redirected_to_login_from_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_letters_index_smoke(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('letters.index'))
            ->assertOk();
    }

    public function test_tasks_index_smoke(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('tasks.index'))
            ->assertOk();
    }

    public function test_notifications_index_smoke(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('notifications.index'))
            ->assertOk();
    }

    public function test_letter_create_form_loads(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('letters.create'))
            ->assertOk();
    }

    public function test_task_create_form_loads(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('tasks.create'))
            ->assertOk();
    }
}
