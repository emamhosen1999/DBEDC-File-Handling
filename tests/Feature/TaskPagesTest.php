<?php

namespace Tests\Feature;

use App\Livewire\Tasks\Create;
use App\Livewire\Tasks\Edit;
use App\Livewire\Tasks\Index;
use App\Livewire\Tasks\Show;
use App\Models\Task;
use App\Models\TaskUpdate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskPagesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $member;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->member = User::factory()->create();
    }

    public function test_tasks_index_loads(): void
    {
        $this->actingAs($this->member)
            ->get(route('tasks.index'))
            ->assertOk()
            ->assertSeeLivewire(Index::class);
    }

    public function test_member_can_create_task(): void
    {
        $this->actingAs($this->member);

        Livewire::test(Create::class)
            ->set('title', 'My test task')
            ->set('status', 'PENDING')
            ->set('priority', 'MEDIUM')
            ->set('assignedTo', $this->member->id)
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHas('tasks', [
            'title' => 'My test task',
            'created_by' => $this->member->id,
        ]);
    }

    public function test_assignee_can_transition_task_status(): void
    {
        $task = Task::factory()->create([
            'assigned_to' => $this->member->id,
            'created_by' => $this->admin->id,
            'status' => 'PENDING',
        ]);

        $this->actingAs($this->member);

        Livewire::test(Show::class, ['task' => $task])
            ->set('newStatus', 'IN_PROGRESS')
            ->set('comment', 'Starting work')
            ->call('applyTransition')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'IN_PROGRESS',
        ]);

        $this->assertDatabaseHas('task_updates', [
            'task_id' => $task->id,
            'user_id' => $this->member->id,
            'old_status' => 'PENDING',
            'new_status' => 'IN_PROGRESS',
        ]);
    }

    public function test_completing_task_sets_completed_at(): void
    {
        $task = Task::factory()->create([
            'assigned_to' => $this->member->id,
            'status' => 'IN_PROGRESS',
            'completed_at' => null,
        ]);

        $this->actingAs($this->member);

        Livewire::test(Show::class, ['task' => $task])
            ->set('newStatus', 'COMPLETED')
            ->call('applyTransition');

        $task->refresh();
        $this->assertEquals('COMPLETED', $task->status);
        $this->assertNotNull($task->completed_at);
    }

    public function test_unrelated_member_cannot_see_others_task(): void
    {
        $task = Task::factory()->create([
            'created_by' => $this->admin->id,
            'assigned_to' => null,
            'department_id' => null,
        ]);

        $this->actingAs($this->member)
            ->get(route('tasks.show', $task))
            ->assertForbidden();
    }
}
