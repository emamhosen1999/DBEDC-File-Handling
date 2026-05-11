<?php

namespace App\Livewire\Letters;

use App\Models\Department;
use App\Models\Letter;
use App\Models\Stakeholder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Letters')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $priority = '';

    #[Url]
    public string $stakeholderId = '';

    #[Url]
    public string $departmentId = '';

    public int $perPage = 15;

    public array $statusOptions = ['DRAFT', 'PENDING', 'IN_PROGRESS', 'REVIEW', 'COMPLETED', 'ARCHIVED'];
    public array $priorityOptions = ['LOW', 'MEDIUM', 'HIGH', 'URGENT'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPriority(): void
    {
        $this->resetPage();
    }

    public function updatingStakeholderId(): void
    {
        $this->resetPage();
    }

    public function updatingDepartmentId(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'status', 'priority', 'stakeholderId', 'departmentId']);
        $this->resetPage();
    }

    public function delete(string $id): void
    {
        $letter = Letter::findOrFail($id);
        $this->authorize('delete', $letter);
        $letter->delete();
        session()->flash('success', 'Letter deleted.');
    }

    public function render()
    {
        $letters = Letter::query()
            ->with(['stakeholder', 'department', 'assignedTo'])
            ->when($this->search, function ($q) {
                $term = '%'.$this->search.'%';
                $q->where(function ($qq) use ($term) {
                    $qq->where('title', 'like', $term)
                        ->orWhere('reference', 'like', $term)
                        ->orWhere('subject', 'like', $term)
                        ->orWhere('sender', 'like', $term)
                        ->orWhere('recipient', 'like', $term);
                });
            })
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->priority, fn ($q) => $q->where('priority', $this->priority))
            ->when($this->stakeholderId, fn ($q) => $q->where('stakeholder_id', $this->stakeholderId))
            ->when($this->departmentId, fn ($q) => $q->where('department_id', $this->departmentId))
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.letters.index', [
            'letters' => $letters,
            'stakeholders' => Stakeholder::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
        ]);
    }
}
