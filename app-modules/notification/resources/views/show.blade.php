@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">{{ $notification->title }}</h1>
                <p class="text-sm text-[hsl(var(--color-muted-foreground))] mt-1">
                    {{ $notification->created_at->format('F j, Y \a\t g:i A') }}
                </p>
            </div>
            <div class="flex gap-2">
                @if(!$notification->read_at)
                    <form action="{{ route('notification::markAsRead', $notification->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <x-shared::button variant="secondary" type="submit">
                            Mark as Read
                        </x-shared::button>
                    </form>
                @endif
                <form action="{{ route('notification::destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this notification?');">
                    @csrf
                    @method('DELETE')
                    <x-shared::button variant="destructive" type="submit">
                        Delete
                    </x-shared::button>
                </form>
            </div>
        </div>

        <x-shared::card>
            <div class="space-y-4">
                <div>
                    <dt class="text-sm font-medium text-[hsl(var(--color-muted-foreground))]">Type</dt>
                    <dd class="mt-1 text-sm text-[hsl(var(--color-foreground))]">{{ $notification->type }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-[hsl(var(--color-muted-foreground))]">Message</dt>
                    <dd class="mt-1 text-sm text-[hsl(var(--color-foreground))] whitespace-pre-wrap">{{ $notification->message }}</dd>
                </div>
                @if($notification->data && count($notification->data) > 0)
                    <div>
                        <dt class="text-sm font-medium text-[hsl(var(--color-muted-foreground))]">Additional Data</dt>
                        <dd class="mt-1 text-sm text-[hsl(var(--color-foreground))]">
                            <pre class="bg-[hsl(var(--color-muted))] p-3 rounded-lg overflow-x-auto">{{ json_encode($notification->data, JSON_PRETTY_PRINT) }}</pre>
                        </dd>
                    </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-[hsl(var(--color-muted-foreground))]">Status</dt>
                    <dd class="mt-1 text-sm">
                        @if($notification->read_at)
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                Read on {{ $notification->read_at->format('F j, Y \a\t g:i A') }}
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-[hsl(var(--color-primary))] px-2.5 py-0.5 text-xs font-medium text-[hsl(var(--color-primary-foreground))]">
                                Unread
                            </span>
                        @endif
                    </dd>
                </div>
            </div>
        </x-shared::card>

        <div class="mt-6">
            <a href="{{ route('notification::index') }}" class="text-sm font-medium text-[hsl(var(--color-primary))] hover:underline">
                ← Back to Notifications
            </a>
        </div>
    </div>
</div>
@endsection
