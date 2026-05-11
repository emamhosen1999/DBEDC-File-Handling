<div>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-white leading-tight iridescent-text">{{ __('Letters') }}</h2>
            <a href="{{ route('letters.create') }}" wire:navigate class="ng-btn ng-btn-primary">+ New letter</a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="glass mb-4 p-3 text-sm text-[var(--color-success)]">{{ session('success') }}</div>
    @endif

    <div class="mb-6 glass p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search title, reference, sender, recipient…" class="ng-input lg:col-span-2">
            <select wire:model.live="status" class="ng-select">
                <option value="">All statuses</option>
                @foreach($statusOptions as $opt)
                    <option value="{{ $opt }}">{{ str_replace('_', ' ', $opt) }}</option>
                @endforeach
            </select>
            <select wire:model.live="priority" class="ng-select">
                <option value="">All priorities</option>
                @foreach($priorityOptions as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                @endforeach
            </select>
            <select wire:model.live="stakeholderId" class="ng-select">
                <option value="">All stakeholders</option>
                @foreach($stakeholders as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
            <select wire:model.live="departmentId" class="ng-select w-full sm:w-auto">
                <option value="">All departments</option>
                @foreach($departments as $d)
                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                @endforeach
            </select>
            <button wire:click="clearFilters" class="ng-btn ng-btn-ghost ng-btn-sm">Clear filters</button>
        </div>
    </div>

    {{-- Mobile cards (below md) --}}
    <div class="grid grid-cols-1 gap-3 md:hidden">
        @forelse($letters as $letter)
            <a href="{{ route('letters.show', $letter) }}" wire:navigate class="glass p-4 block">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <div class="text-xs eyebrow text-[var(--color-fg-subtle)]">{{ $letter->reference }}</div>
                        <div class="mt-1 font-semibold text-[var(--color-fg)] break-words">{{ $letter->title }}</div>
                    </div>
                    <span class="ng-badge ng-badge-plasma whitespace-nowrap">{{ str_replace('_',' ',$letter->status) }}</span>
                </div>
                <div class="mt-2 flex flex-wrap gap-2 text-xs text-[var(--color-fg-muted)]">
                    @if($letter->stakeholder)<span class="ng-badge" style="background: {{ $letter->stakeholder->color }}22; color: {{ $letter->stakeholder->color }}">{{ $letter->stakeholder->code }}</span>@endif
                    <span>Priority: {{ $letter->priority }}</span>
                    <span>{{ $letter->letter_date?->format('M d, Y') }}</span>
                </div>
            </a>
        @empty
            <div class="glass p-8 text-center text-sm text-[var(--color-fg-subtle)]">No letters found.</div>
        @endforelse
    </div>

    {{-- Desktop table (md+) --}}
    <div class="hidden md:block glass overflow-x-auto">
        <table class="min-w-full text-left">
            <thead>
                <tr class="border-b border-[rgba(255,255,255,0.1)]">
                    <th class="px-4 py-3 eyebrow">Reference</th>
                    <th class="px-4 py-3 eyebrow">Title</th>
                    <th class="px-4 py-3 eyebrow">Stakeholder</th>
                    <th class="px-4 py-3 eyebrow">Priority</th>
                    <th class="px-4 py-3 eyebrow">Status</th>
                    <th class="px-4 py-3 eyebrow">Date</th>
                    <th class="px-4 py-3 eyebrow"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[rgba(255,255,255,0.05)]">
                @forelse($letters as $letter)
                    <tr class="hover:bg-[rgba(255,255,255,0.05)] transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-[var(--color-fg-muted)]">{{ $letter->reference }}</td>
                        <td class="px-4 py-3 text-sm text-[var(--color-fg)]">
                            <a href="{{ route('letters.show', $letter) }}" wire:navigate class="hover:underline">{{ $letter->title }}</a>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            @if($letter->stakeholder)
                                <span class="ng-badge" style="background: {{ $letter->stakeholder->color }}22; color: {{ $letter->stakeholder->color }}">{{ $letter->stakeholder->code }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $letter->priority }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <span class="ng-badge {{ $letter->status === 'COMPLETED' ? 'ng-badge-aurora' : ($letter->status === 'IN_PROGRESS' ? 'ng-badge-electric' : 'ng-badge-plasma') }}">
                                {{ str_replace('_',' ',$letter->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-[var(--color-fg-muted)]">{{ $letter->letter_date?->format('M d, Y') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right">
                            <a href="{{ route('letters.show', $letter) }}" wire:navigate class="ng-btn ng-btn-sm ng-btn-ghost">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-[var(--color-fg-subtle)]">No letters found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $letters->links() }}</div>
</div>
