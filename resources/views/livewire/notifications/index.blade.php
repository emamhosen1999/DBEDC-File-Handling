<div>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-white leading-tight iridescent-text">{{ __('Notifications') }}</h2>
            @if($unreadCount > 0)
                <button wire:click="markAllRead" class="ng-btn ng-btn-ghost">Mark all read</button>
            @endif
        </div>
    </x-slot>

    @if (session('success'))
        <div class="glass mb-4 p-3 text-sm text-[var(--color-success)]">{{ session('success') }}</div>
    @endif

    <div class="glass p-2 mb-4 inline-flex gap-1">
        <button wire:click="setFilter('all')" class="ng-btn ng-btn-sm {{ $filter === 'all' ? 'ng-btn-primary' : 'ng-btn-ghost' }}">All</button>
        <button wire:click="setFilter('unread')" class="ng-btn ng-btn-sm {{ $filter === 'unread' ? 'ng-btn-primary' : 'ng-btn-ghost' }}">Unread ({{ $unreadCount }})</button>
    </div>

    <div class="glass divide-y divide-[rgba(255,255,255,0.05)]">
        @forelse($notifications as $n)
            <div class="p-4 flex items-start gap-3 {{ $n->is_read ? '' : 'bg-[rgba(255,140,220,0.06)]' }}">
                <span class="mt-1 inline-block w-2 h-2 rounded-full shrink-0 {{ $n->is_read ? 'bg-transparent' : 'bg-[var(--color-primary)]' }}"></span>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-baseline gap-2">
                        <span class="ng-badge {{ ['INFO' => 'ng-badge-electric', 'SUCCESS' => 'ng-badge-aurora', 'WARNING' => 'ng-badge-plasma', 'ERROR' => 'ng-badge-plasma'][$n->type] ?? 'ng-badge-electric' }}">{{ $n->type }}</span>
                        <div class="font-semibold text-[var(--color-fg)] break-words">{{ $n->title }}</div>
                        <div class="text-xs text-[var(--color-fg-subtle)] ml-auto">{{ $n->created_at?->diffForHumans() }}</div>
                    </div>
                    @if($n->message)
                        <div class="mt-1 text-sm text-[var(--color-fg-muted)] whitespace-pre-wrap">{{ $n->message }}</div>
                    @endif
                    <div class="mt-2 flex flex-wrap gap-2">
                        @if($n->link)
                            <a href="{{ $n->link }}" class="ng-btn ng-btn-sm ng-btn-ghost">Open</a>
                        @endif
                        @if(! $n->is_read)
                            <button wire:click="markRead('{{ $n->id }}')" class="ng-btn ng-btn-sm ng-btn-ghost">Mark read</button>
                        @endif
                        <button wire:click="destroy('{{ $n->id }}')" wire:confirm="Delete this notification?" class="ng-btn ng-btn-sm ng-btn-ghost text-[var(--color-danger)]">Delete</button>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-sm text-[var(--color-fg-subtle)]">No notifications.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $notifications->links() }}</div>
</div>
