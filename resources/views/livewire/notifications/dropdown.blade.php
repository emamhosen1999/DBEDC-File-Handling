<div x-data="{ open: false }" class="relative" wire:poll.60s="refreshCount">
    <button @click="open = !open" class="relative inline-flex items-center px-3 py-2 border border-white/20 rounded-md bg-white/10 hover:bg-white/20 text-gray-200">
        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        @if($unread > 0)
            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-5 h-5 px-1 text-xs font-bold rounded-full bg-[var(--color-primary)] text-white shadow-[0_0_10px_var(--color-primary)]">{{ $unread > 99 ? '99+' : $unread }}</span>
        @endif
    </button>

    <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-80 max-w-[90vw] glass rounded-md z-50">
        <div class="px-4 py-3 border-b border-white/10 flex items-center justify-between">
            <span class="eyebrow">Notifications</span>
            <a href="{{ route('notifications.index') }}" wire:navigate class="text-xs hover:underline text-[var(--color-fg-muted)]">View all</a>
        </div>
        <div class="max-h-96 overflow-y-auto divide-y divide-white/5">
            @forelse($recent as $n)
                <a href="{{ $n->link ?: route('notifications.index') }}" class="block px-4 py-3 hover:bg-white/5 {{ $n->is_read ? '' : 'bg-[rgba(255,140,220,0.06)]' }}">
                    <div class="text-sm text-[var(--color-fg)] font-medium truncate">{{ $n->title }}</div>
                    @if($n->message)
                        <div class="text-xs text-[var(--color-fg-muted)] truncate mt-0.5">{{ $n->message }}</div>
                    @endif
                    <div class="text-xs text-[var(--color-fg-subtle)] mt-1">{{ $n->created_at?->diffForHumans() }}</div>
                </a>
            @empty
                <div class="px-4 py-6 text-center text-sm text-[var(--color-fg-subtle)]">No notifications</div>
            @endforelse
        </div>
    </div>
</div>
