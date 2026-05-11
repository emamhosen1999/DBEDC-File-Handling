<?php

namespace App\Livewire\Admin\Stakeholders;

use App\Models\Stakeholder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Stakeholders')]
class Index extends Component
{
    public bool $showForm = false;
    public ?string $editingId = null;

    public string $name = '';
    public string $code = '';
    public string $color = '#6B7280';
    public string $description = '';
    public bool $isActive = true;

    public function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'code', 'description']);
        $this->color = '#6B7280';
        $this->isActive = true;
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(string $id): void
    {
        $s = Stakeholder::findOrFail($id);
        $this->editingId = $s->id;
        $this->name = $s->name;
        $this->code = $s->code;
        $this->color = $s->color;
        $this->description = (string) $s->description;
        $this->isActive = (bool) $s->is_active;
        $this->showForm = true;
    }

    public function close(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:100|unique:stakeholders,name'.($this->editingId ? ','.$this->editingId : ''),
            'code' => 'required|string|max:20|unique:stakeholders,code'.($this->editingId ? ','.$this->editingId : ''),
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string',
        ]);

        $data = [
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'color' => $this->color,
            'description' => $this->description ?: null,
            'is_active' => $this->isActive,
        ];

        if ($this->editingId) {
            Stakeholder::where('id', $this->editingId)->update($data);
            session()->flash('success', 'Stakeholder updated.');
        } else {
            Stakeholder::create($data);
            session()->flash('success', 'Stakeholder created.');
        }

        $this->close();
    }

    public function delete(string $id): void
    {
        $s = Stakeholder::findOrFail($id);
        if ($s->letters()->exists()) {
            session()->flash('error', 'Cannot delete: stakeholder has letters.');
            return;
        }
        $s->delete();
        session()->flash('success', 'Stakeholder deleted.');
    }

    public function render()
    {
        return view('livewire.admin.stakeholders.index', [
            'stakeholders' => Stakeholder::orderBy('name')->get(),
        ]);
    }
}
