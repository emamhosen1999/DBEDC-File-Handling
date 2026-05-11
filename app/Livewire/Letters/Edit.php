<?php

namespace App\Livewire\Letters;

use App\Models\Department;
use App\Models\Letter;
use App\Models\Stakeholder;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Edit Letter')]
class Edit extends Component
{
    use WithFileUploads;

    public Letter $letter;

    public string $reference = '';
    public string $title = '';
    public string $description = '';
    public string $sender = '';
    public string $recipient = '';
    public string $subject = '';
    public string $letterDate = '';
    public string $dueDate = '';
    public string $priority = 'MEDIUM';
    public string $status = 'PENDING';
    public string $departmentId = '';
    public string $assignedTo = '';
    public string $stakeholderId = '';

    public $attachment = null;

    public function mount(Letter $letter): void
    {
        Gate::authorize('update', $letter);
        $this->letter = $letter;
        $this->reference = $letter->reference;
        $this->title = $letter->title;
        $this->description = (string) $letter->description;
        $this->sender = (string) $letter->sender;
        $this->recipient = (string) $letter->recipient;
        $this->subject = (string) $letter->subject;
        $this->letterDate = $letter->letter_date?->toDateString() ?? '';
        $this->dueDate = $letter->due_date?->toDateString() ?? '';
        $this->priority = $letter->priority;
        $this->status = $letter->status;
        $this->departmentId = (string) $letter->department_id;
        $this->assignedTo = (string) $letter->assigned_to;
        $this->stakeholderId = (string) $letter->stakeholder_id;
    }

    public function save(FileUploadService $uploads)
    {
        $this->validate([
            'reference' => 'required|string|max:100|unique:letters,reference,'.$this->letter->id,
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'sender' => 'nullable|string|max:255',
            'recipient' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:500',
            'letterDate' => 'required|date',
            'dueDate' => 'nullable|date',
            'priority' => 'required|in:LOW,MEDIUM,HIGH,URGENT',
            'status' => 'required|in:DRAFT,PENDING,IN_PROGRESS,REVIEW,COMPLETED,ARCHIVED',
            'departmentId' => 'nullable|exists:departments,id',
            'assignedTo' => 'nullable|exists:users,id',
            'stakeholderId' => 'required|exists:stakeholders,id',
            'attachment' => 'nullable|file|max:20480',
        ]);

        $data = [
            'reference' => $this->reference,
            'title' => $this->title,
            'description' => $this->description ?: null,
            'sender' => $this->sender ?: null,
            'recipient' => $this->recipient ?: null,
            'subject' => $this->subject ?: null,
            'letter_date' => $this->letterDate,
            'due_date' => $this->dueDate ?: null,
            'priority' => $this->priority,
            'status' => $this->status,
            'department_id' => $this->departmentId ?: null,
            'assigned_to' => $this->assignedTo ?: null,
            'stakeholder_id' => $this->stakeholderId,
        ];

        if ($this->attachment) {
            if ($this->letter->file_path) {
                Storage::disk('local')->delete($this->letter->file_path);
            }
            $data = array_merge($data, $uploads->store($this->attachment, 'letters'));
        }

        if ($this->status === 'COMPLETED' && ! $this->letter->completed_at) {
            $data['completed_at'] = now();
        }

        $this->letter->update($data);

        session()->flash('success', 'Letter updated.');
        return $this->redirectRoute('letters.show', $this->letter->id, navigate: true);
    }

    public function render()
    {
        return view('livewire.letters.form', [
            'mode' => 'edit',
            'letter' => $this->letter,
            'stakeholders' => Stakeholder::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
            'users' => User::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
