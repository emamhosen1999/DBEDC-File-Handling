<?php

namespace App\Livewire\Letters;

use App\Models\Department;
use App\Models\Letter;
use App\Models\Stakeholder;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('New Letter')]
class Create extends Component
{
    use WithFileUploads;

    #[Validate('required|string|max:100')]
    public string $reference = '';

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('nullable|string|max:2000')]
    public string $description = '';

    #[Validate('nullable|string|max:255')]
    public string $sender = '';

    #[Validate('nullable|string|max:255')]
    public string $recipient = '';

    #[Validate('nullable|string|max:500')]
    public string $subject = '';

    #[Validate('required|date')]
    public string $letterDate = '';

    #[Validate('nullable|date')]
    public string $dueDate = '';

    #[Validate('required|in:LOW,MEDIUM,HIGH,URGENT')]
    public string $priority = 'MEDIUM';

    #[Validate('required|in:DRAFT,PENDING,IN_PROGRESS,REVIEW,COMPLETED,ARCHIVED')]
    public string $status = 'PENDING';

    #[Validate('nullable|exists:departments,id')]
    public string $departmentId = '';

    #[Validate('nullable|exists:users,id')]
    public string $assignedTo = '';

    #[Validate('required|exists:stakeholders,id')]
    public string $stakeholderId = '';

    #[Validate('nullable|file|max:20480|mimes:pdf,png,jpg,jpeg,doc,docx,xls,xlsx')]
    public $attachment = null;

    public function mount(): void
    {
        Gate::authorize('create', Letter::class);
        $this->letterDate = now()->toDateString();
    }

    public function save(FileUploadService $uploads)
    {
        $this->validate([
            'reference' => 'required|string|max:100|unique:letters,reference',
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
            'created_by' => auth()->id(),
        ];

        if ($this->attachment) {
            $stored = $uploads->store($this->attachment, 'letters');
            $data = array_merge($data, $stored);
        }

        $letter = Letter::create($data);

        session()->flash('success', 'Letter created.');
        return $this->redirectRoute('letters.show', $letter->id, navigate: true);
    }

    public function render()
    {
        return view('livewire.letters.form', [
            'mode' => 'create',
            'stakeholders' => Stakeholder::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
            'users' => User::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
