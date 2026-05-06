<?php

namespace Tests\Feature;

use App\Models\Letter;
use App\Models\User;
use App\Models\Department;
use App\Models\Stakeholder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LetterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'ADMIN']);
        $this->actingAs($this->user);
    }

    public function test_can_list_letters(): void
    {
        Letter::factory()->count(5)->create();

        $response = $this->getJson('/api/letters');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'letters' => [],
                'pagination' => [
                    'page',
                    'per_page',
                    'total',
                    'total_pages',
                ],
            ]);
    }

    public function test_can_create_letter(): void
    {
        $department = Department::factory()->create();
        $stakeholder = Stakeholder::factory()->create();

        $data = [
            'reference' => 'TEST-' . $this->faker->unique()->numberBetween(1000, 9999),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'sender' => $this->faker->name,
            'recipient' => $this->faker->name,
            'subject' => $this->faker->sentence,
            'letter_date' => $this->faker->date,
            'due_date' => $this->faker->date,
            'priority' => 'MEDIUM',
            'status' => 'PENDING',
            'department_id' => $department->id,
            'assigned_to' => $this->user->id,
            'stakeholder_id' => $stakeholder->id,
        ];

        $response = $this->postJson('/api/letters', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Letter created successfully',
            ]);

        $this->assertDatabaseHas('letters', [
            'reference' => $data['reference'],
            'title' => $data['title'],
        ]);
    }

    public function test_can_show_letter(): void
    {
        $letter = Letter::factory()->create();

        $response = $this->getJson("/api/letters/{$letter->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $letter->id,
                'reference' => $letter->reference,
                'title' => $letter->title,
            ]);
    }

    public function test_can_update_letter(): void
    {
        $letter = Letter::factory()->create();

        $data = [
            'title' => 'Updated Title',
            'status' => 'IN_PROGRESS',
        ];

        $response = $this->putJson("/api/letters/{$letter->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Letter updated successfully',
            ]);

        $this->assertDatabaseHas('letters', [
            'id' => $letter->id,
            'title' => 'Updated Title',
            'status' => 'IN_PROGRESS',
        ]);
    }

    public function test_can_delete_letter(): void
    {
        $letter = Letter::factory()->create();

        $response = $this->deleteJson("/api/letters/{$letter->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Letter deleted successfully',
            ]);

        $this->assertSoftDeleted('letters', [
            'id' => $letter->id,
        ]);
    }

    public function test_requires_authentication(): void
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->getJson('/api/letters');

        $response->assertStatus(401);
    }
}
