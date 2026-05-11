<?php

namespace App\Livewire\Admin\Activities;

use App\Models\Activity;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Activity Log')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with('user')
            ->when($this->search, function ($q) {
                $term = '%'.$this->search.'%';
                $q->where(function ($qq) use ($term) {
                    $qq->where('action', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhere('entity_type', 'like', $term);
                });
            })
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('livewire.admin.activities.index', [
            'activities' => $activities,
        ]);
    }
}
