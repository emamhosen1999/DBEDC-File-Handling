<div>
    <div class="mb-6 glass p-4">
        <input type="text" 
               wire:model.live="search" 
               placeholder="Search letters..." 
               class="ng-input">
    </div>

    <div class="glass overflow-hidden">
        <table class="min-w-full text-left">
            <thead>
                <tr class="border-b border-[rgba(255,255,255,0.1)]">
                    <th class="px-6 py-4 eyebrow">Reference</th>
                    <th class="px-6 py-4 eyebrow">Subject</th>
                    <th class="px-6 py-4 eyebrow">Date</th>
                    <th class="px-6 py-4 eyebrow">Status</th>
                    <th class="px-6 py-4 eyebrow">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[rgba(255,255,255,0.05)]">
                @forelse($letters as $letter)
                    <tr class="hover:bg-[rgba(255,255,255,0.05)] transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-fg-subtle)]">{{ $letter->reference_number ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $letter->subject }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-fg-subtle)]">{{ $letter->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="ng-badge {{ $letter->status === 'received' ? 'ng-badge-aurora' : 'ng-badge-plasma' }}">
                                {{ $letter->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <a href="{{ route('letters.show', $letter->id) }}" class="ng-btn ng-btn-sm ng-btn-ghost">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-[var(--color-fg-subtle)]">No letters found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $letters->links() }}
    </div>
</div>
