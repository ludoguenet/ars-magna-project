@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold tracking-tight">Edit Invoice</h1>
            <p class="text-sm text-[hsl(var(--color-muted-foreground))] mt-1">Update invoice details</p>
        </div>

        <x-shared::card>
            <form action="{{ route('invoice::update', $invoice->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="client_id" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                            Client <span class="text-[hsl(var(--color-destructive))]">*</span>
                        </label>
                        <select
                            name="client_id"
                            id="client_id"
                            required
                            class="flex h-10 w-full rounded-lg border border-[hsl(var(--color-input))] bg-[hsl(var(--color-background))] px-3 py-2 text-sm ring-offset-[hsl(var(--color-background))] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[hsl(var(--color-ring))] focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <option value="">Select a client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', $invoice->clientId) == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <p class="text-sm font-medium text-[hsl(var(--color-destructive))]">{{ $message }}</p>
                        @enderror
                    </div>

                    <x-shared::input
                        type="date"
                        name="issued_at"
                        label="Issue Date"
                        :value="old('issued_at', $invoice->issuedAt?->format('Y-m-d'))"
                        required
                        :error="isset($errors) ? $errors->first('issued_at') : null"
                    />

                    <x-shared::input
                        type="date"
                        name="due_at"
                        label="Due Date"
                        :value="old('due_at', $invoice->dueAt?->format('Y-m-d'))"
                        :error="isset($errors) ? $errors->first('due_at') : null"
                    />
                </div>

                <div class="space-y-2">
                    <label for="notes" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                        Notes
                    </label>
                    <textarea
                        name="notes"
                        id="notes"
                        rows="4"
                        class="flex min-h-[80px] w-full rounded-lg border border-[hsl(var(--color-input))] bg-[hsl(var(--color-background))] px-3 py-2 text-sm ring-offset-[hsl(var(--color-background))] placeholder:text-[hsl(var(--color-muted-foreground))] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[hsl(var(--color-ring))] focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >{{ old('notes', $invoice->notes) }}</textarea>
                    @error('notes')
                        <p class="text-sm font-medium text-[hsl(var(--color-destructive))]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="terms" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                        Terms
                    </label>
                    <textarea
                        name="terms"
                        id="terms"
                        rows="4"
                        class="flex min-h-[80px] w-full rounded-lg border border-[hsl(var(--color-input))] bg-[hsl(var(--color-background))] px-3 py-2 text-sm ring-offset-[hsl(var(--color-background))] placeholder:text-[hsl(var(--color-muted-foreground))] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[hsl(var(--color-ring))] focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >{{ old('terms', $invoice->terms) }}</textarea>
                    @error('terms')
                        <p class="text-sm font-medium text-[hsl(var(--color-destructive))]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <x-shared::button type="submit" variant="primary">
                        Save Changes
                    </x-shared::button>
                    <a href="{{ route('invoice::show', $invoice->id) }}">
                        <x-shared::button variant="outline" type="button">
                            Cancel
                        </x-shared::button>
                    </a>
                </div>
            </form>
        </x-shared::card>
    </div>
</div>
@endsection
