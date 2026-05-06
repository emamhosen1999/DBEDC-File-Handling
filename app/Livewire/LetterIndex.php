<?php

namespace App\Livewire;

use App\Models\Letter;
use Livewire\Component;
use Livewire\WithPagination;

class LetterIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    public function render()
    {
        $letters = Letter::when($this->search, function ($query) {
            $query->where('subject', 'like', '%' . $this->search . '%')
                  ->orWhere('reference_number', 'like', '%' . $this->search . '%');
        })->paginate($this->perPage);

        return view('livewire.letter-index', [
            'letters' => $letters,
        ]);
    }
}
