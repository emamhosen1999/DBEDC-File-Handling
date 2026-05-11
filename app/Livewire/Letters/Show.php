<?php

namespace App\Livewire\Letters;

use App\Models\Letter;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Letter')]
class Show extends Component
{
    public Letter $letter;

    public function mount(Letter $letter): void
    {
        Gate::authorize('view', $letter);
        $this->letter = $letter->load(['stakeholder', 'department', 'assignedTo', 'createdBy', 'tasks.assignedTo']);
    }

    public function delete()
    {
        Gate::authorize('delete', $this->letter);
        $this->letter->delete();
        session()->flash('success', 'Letter deleted.');
        return $this->redirectRoute('letters.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.letters.show');
    }
}
