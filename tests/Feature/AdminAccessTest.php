<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_cannot_access_admin(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_manager_can_access_admin(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_admin_can_access_admin(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_admin_can_access_users_page(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    public function test_admin_can_access_departments_page(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.departments.index'))
            ->assertOk();
    }

    public function test_admin_can_access_stakeholders_page(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.stakeholders.index'))
            ->assertOk();
    }

    public function test_admin_can_access_activities_page(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.activities.index'))
            ->assertOk();
    }
}
