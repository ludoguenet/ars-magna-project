@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Clients</h1>
            <p class="text-sm text-[hsl(var(--color-muted-foreground))] mt-1">Manage your client relationships</p>
        </div>
        <a href="{{ route('client::create') }}">
            <x-shared::button variant="primary">
                New Client
            </x-shared::button>
        </a>
    </div>

    <x-shared::card>
        <div class="overflow-x-auto">
            <table class="w-full caption-bottom text-sm">
                <thead>
                    <tr class="border-b border-[hsl(var(--color-border))] transition-colors hover:bg-[hsl(var(--color-muted))]/50">
                        <th class="h-12 px-4 text-left align-middle font-medium text-[hsl(var(--color-muted-foreground))]">
                            Name
                        </th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-[hsl(var(--color-muted-foreground))]">
                            Company
                        </th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-[hsl(var(--color-muted-foreground))]">
                            Email
                        </th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-[hsl(var(--color-muted-foreground))]">
                            Phone
                        </th>
                        <th class="h-12 px-4 text-right align-middle font-medium text-[hsl(var(--color-muted-foreground))]">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr class="border-b border-[hsl(var(--color-border))] transition-colors hover:bg-[hsl(var(--color-muted))]/50">
                            <td class="p-4 align-middle font-medium">
                                {{ $client->name }}
                            </td>
                            <td class="p-4 align-middle text-[hsl(var(--color-muted-foreground))]">
                                {{ $client->company ?? '-' }}
                            </td>
                            <td class="p-4 align-middle text-[hsl(var(--color-muted-foreground))]">
                                {{ $client->email ?? '-' }}
                            </td>
                            <td class="p-4 align-middle text-[hsl(var(--color-muted-foreground))]">
                                {{ $client->phone ?? '-' }}
                            </td>
                            <td class="p-4 align-middle text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('client::show', $client->id) }}" class="text-sm font-medium text-[hsl(var(--color-primary))] hover:underline">
                                        View
                                    </a>
                                    <span class="text-[hsl(var(--color-border))]">|</span>
                                    <a href="{{ route('client::edit', $client->id) }}" class="text-sm font-medium text-[hsl(var(--color-primary))] hover:underline">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-sm text-[hsl(var(--color-muted-foreground))]">
                                No clients found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-shared::card>
</div>
@endsection
