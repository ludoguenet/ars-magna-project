@props(['notifications' => [], 'unreadCount' => 0])

<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="relative inline-flex items-center justify-center rounded-lg p-2 text-[hsl(var(--color-muted-foreground))] transition-colors hover:bg-[hsl(var(--color-accent))] hover:text-[hsl(var(--color-foreground))]">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-[hsl(var(--color-destructive))] ring-2 ring-[hsl(var(--color-background))]"></span>
        @endif
    </button>

    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 rounded-lg border border-[hsl(var(--color-border))] bg-[hsl(var(--color-card))] shadow-lg z-50 max-h-96 overflow-y-auto">
        <div class="p-4 border-b border-[hsl(var(--color-border))]">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold">Notifications</h3>
                @if($unreadCount > 0)
                    <form action="{{ route('notification::markAllAsRead') }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-xs text-[hsl(var(--color-primary))] hover:underline">
                            Mark all as read
                        </button>
                    </form>
                @endif
            </div>
        </div>
        <div class="divide-y divide-[hsl(var(--color-border))]">
            @forelse($notifications->take(5) as $notification)
                <a href="{{ route('notification::show', $notification->id) }}" class="block p-4 hover:bg-[hsl(var(--color-accent))] transition-colors {{ !$notification->read_at ? 'bg-[hsl(var(--color-accent))]/30' : '' }}">
                    <div class="flex items-start gap-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-[hsl(var(--color-foreground))] truncate">{{ $notification->title }}</p>
                            <p class="text-xs text-[hsl(var(--color-muted-foreground))] mt-1 line-clamp-2">{{ $notification->message }}</p>
                            <p class="text-xs text-[hsl(var(--color-muted-foreground))] mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        @if(!$notification->read_at)
                            <span class="flex-shrink-0 h-2 w-2 rounded-full bg-[hsl(var(--color-primary))]"></span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="p-4 text-center text-sm text-[hsl(var(--color-muted-foreground))]">
                    No notifications
                </div>
            @endforelse
        </div>
        @if($notifications->count() > 5)
            <div class="p-4 border-t border-[hsl(var(--color-border))] text-center">
                <a href="{{ route('notification::index') }}" class="text-sm text-[hsl(var(--color-primary))] hover:underline">
                    View all notifications
                </a>
            </div>
        @endif
    </div>
</div>
