<?php

namespace Tests\Feature;

use App\Livewire\Letters\Create;
use App\Livewire\Letters\Edit;
use App\Livewire\Letters\Index;
use App\Livewire\Letters\Show;
use App\Models\Department;
use App\Models\Letter;
use App\Models\Stakeholder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class LetterPagesTest extends TestCase
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

    public function test_letter_index_loads_for_authenticated_user(): void
    {
        $this->actingAs($this->member)
            ->get(route('letters.index'))
            ->assertOk()
            ->assertSeeLivewire(Index::class);
    }

    public function test_letter_index_filters_by_search(): void
    {
        $stakeholder = Stakeholder::factory()->create();
        $matching = Letter::factory()->create(['title' => 'Unique alpha title', 'stakeholder_id' => $stakeholder->id]);
        $other = Letter::factory()->create(['title' => 'Zeta totally different', 'stakeholder_id' => $stakeholder->id]);

        $this->actingAs($this->member);

        Livewire::test(Index::class)
            ->set('search', 'alpha')
            ->assertSee($matching->title)
            ->assertDontSee($other->title);
    }

    public function test_member_can_create_letter(): void
    {
        Storage::fake('local');
        $stakeholder = Stakeholder::factory()->create();
        $department = Department::factory()->create();

        $this->actingAs($this->member);

        Livewire::test(Create::class)
            ->set('reference', 'L-TEST-001')
            ->set('title', 'My new letter')
            ->set('letterDate', now()->toDateString())
            ->set('stakeholderId', $stakeholder->id)
            ->set('departmentId', $department->id)
            ->set('priority', 'HIGH')
            ->set('status', 'PENDING')
            ->set('attachment', UploadedFile::fake()->create('test.pdf', 100, 'application/pdf'))
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHas('letters', [
            'reference' => 'L-TEST-001',
            'title' => 'My new letter',
            'priority' => 'HIGH',
        ]);
    }

    public function test_admin_can_view_any_letter(): void
    {
        $letter = Letter::factory()->create(['sender' => 'Sender Test Name', 'subject' => 'Subject Test Text']);

        $this->actingAs($this->admin);

        Livewire::test(Show::class, ['letter' => $letter])
            ->assertOk()
            ->assertSee('Sender Test Name')
            ->assertSee('Subject Test Text');
    }

    public function test_creator_can_edit_their_letter(): void
    {
        $letter = Letter::factory()->create(['created_by' => $this->member->id]);

        $this->actingAs($this->member);

        Livewire::test(Edit::class, ['letter' => $letter])
            ->set('title', 'Updated title here')
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHas('letters', [
            'id' => $letter->id,
            'title' => 'Updated title here',
        ]);
    }

    public function test_non_creator_member_cannot_edit_letter_they_dont_own(): void
    {
        $owner = User::factory()->create();
        // Member not the creator, not assigned, different (no) department -> not authorized
        $letter = Letter::factory()->create([
            'created_by' => $owner->id,
            'assigned_to' => null,
            'department_id' => null,
        ]);

        $this->actingAs($this->member)
            ->get(route('letters.edit', $letter))
            ->assertForbidden();
    }

    public function test_letter_download_returns_file(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('letter.pdf', 50);
        $path = Storage::disk('local')->putFile('letters', $file);
        $letter = Letter::factory()->create([
            'file_path' => $path,
            'file_name' => 'letter.pdf',
            'file_mime_type' => 'application/pdf',
            'created_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin)
            ->get(route('letters.download', $letter))
            ->assertOk()
            ->assertHeader('content-disposition');
    }

    public function test_letter_download_404_when_no_attachment(): void
    {
        $letter = Letter::factory()->create(['file_path' => null]);

        $this->actingAs($this->admin)
            ->get(route('letters.download', $letter))
            ->assertNotFound();
    }
}
