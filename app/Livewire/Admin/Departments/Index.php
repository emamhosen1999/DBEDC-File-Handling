<?php

namespace App\Livewire\Admin\Departments;

use App\Models\Department;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Departments')]
class Index extends Component
{
    public bool $showForm = false;
    public ?string $editingId = null;

    public string $name = '';
    public string $description = '';
    public string $parentId = '';
    public string $managerId = '';
    public bool $isActive = true;
    public int $displayOrder = 0;

    public function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'description', 'parentId', 'managerId', 'isActive', 'displayOrder']);
        $this->isActive = true;
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(string $id): void
    {
        $d = Department::findOrFail($id);
        $this->editingId = $d->id;
        $this->name = $d->name;
        $this->description = (string) $d->description;
        $this->parentId = (string) $d->parent_id;
        $this->managerId = (string) $d->manager_id;
        $this->isActive = (bool) $d->is_active;
        $this->displayOrder = (int) $d->display_order;
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
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'parentId' => 'nullable|exists:departments,id',
            'managerId' => 'nullable|exists:users,id',
            'displayOrder' => 'integer',
        ]);

        $data = [
            'name' => $this->name,
            'description' => $this->description ?: null,
            'parent_id' => $this->parentId ?: null,
            'manager_id' => $this->managerId ?: null,
            'is_active' => $this->isActive,
            'display_order' => $this->displayOrder,
        ];

        if ($this->editingId) {
            Department::where('id', $this->editingId)->update($data);
            session()->flash('success', 'Department updated.');
        } else {
            Department::create($data);
            session()->flash('success', 'Department created.');
        }

        $this->close();
    }

    public function delete(string $id): void
    {
        $d = Department::findOrFail($id);
        if ($d->children()->exists() || $d->users()->exists()) {
            session()->flash('error', 'Cannot delete: department has children or users.');
            return;
        }
        $d->delete();
        session()->flash('success', 'Department deleted.');
    }

    public function render()
    {
        return view('livewire.admin.departments.index', [
            'departments' => Department::with(['parent', 'manager'])->orderBy('display_order')->orderBy('name')->get(),
            'parents' => Department::orderBy('name')->get(),
            'users' => User::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
