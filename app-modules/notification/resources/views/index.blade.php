@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Notifications</h1>
            <p class="text-sm text-[hsl(var(--color-muted-foreground))] mt-1">Manage your notifications</p>
        </div>
        @if($notifications->whereNull('read_at')->count() > 0)
            <form action="{{ route('notification::markAllAsRead') }}" method="POST">
                @csrf
                @method('PATCH')
                <x-shared::button variant="secondary" type="submit">
                    Mark All as Read
                </x-shared::button>
            </form>
        @endif
    </div>

    <x-shared::card>
        @if($notifications->count() > 0)
            <div class="divide-y divide-[hsl(var(--color-border))]">
                @foreach($notifications as $notification)
                    <div class="p-4 hover:bg-[hsl(var(--color-muted))]/50 transition-colors {{ $notification->read_at ? '' : 'bg-[hsl(var(--color-accent))]/30' }}">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="font-semibold text-[hsl(var(--color-foreground))]">{{ $notification->title }}</h3>
                                    @if(!$notification->read_at)
                                        <span class="inline-flex items-center rounded-full bg-[hsl(var(--color-primary))] px-2 py-0.5 text-xs font-medium text-[hsl(var(--color-primary-foreground))]">
                                            New
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-[hsl(var(--color-muted-foreground))] mb-2">{{ $notification->message }}</p>
                                <div class="flex items-center gap-4 text-xs text-[hsl(var(--color-muted-foreground))]">
                                    <span>{{ $notification->type }}</span>
                                    <span>{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if(!$notification->read_at)
                                    <form action="{{ route('notification::markAsRead', $notification->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <x-shared::button variant="ghost" type="submit" class="text-xs">
                                            Mark as Read
                                        </x-shared::button>
                                    </form>
                                @endif
                                <a href="{{ route('notification::show', $notification->id) }}" class="text-sm font-medium text-[hsl(var(--color-primary))] hover:underline">
                                    View
                                </a>
                                <form action="{{ route('notification::destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this notification?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-[hsl(var(--color-destructive))] hover:underline">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-sm text-[hsl(var(--color-muted-foreground))]">No notifications found</p>
            </div>
        @endif
    </x-shared::card>
</div>
@endsection
