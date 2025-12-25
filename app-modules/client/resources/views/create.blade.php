@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold tracking-tight">New Client</h1>
            <p class="text-sm text-[hsl(var(--color-muted-foreground))] mt-1">Add a new client to your system</p>
        </div>

        <x-shared::card>
            <form action="{{ route('client::store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-shared::input
                        name="name"
                        label="Name"
                        required
                        :error="isset($errors) ? $errors->first('name') : null"
                    />

                    <x-shared::input
                        name="company"
                        label="Company"
                        :error="isset($errors) ? $errors->first('company') : null"
                    />

                    <x-shared::input
                        type="email"
                        name="email"
                        label="Email"
                        :error="isset($errors) ? $errors->first('email') : null"
                    />

                    <x-shared::input
                        type="tel"
                        name="phone"
                        label="Phone"
                        :error="isset($errors) ? $errors->first('phone') : null"
                    />

                    <x-shared::input
                        name="vat_number"
                        label="VAT Number"
                        :error="isset($errors) ? $errors->first('vat_number') : null"
                    />
                </div>

                <x-shared::input
                    name="address"
                    label="Address"
                    :error="isset($errors) ? $errors->first('address') : null"
                />

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-shared::input
                        name="postal_code"
                        label="Postal Code"
                        :error="isset($errors) ? $errors->first('postal_code') : null"
                    />

                    <x-shared::input
                        name="city"
                        label="City"
                        :error="isset($errors) ? $errors->first('city') : null"
                    />

                    <x-shared::input
                        name="country"
                        label="Country"
                        :error="isset($errors) ? $errors->first('country') : null"
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
                    >{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-sm font-medium text-[hsl(var(--color-destructive))]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <x-shared::button type="submit" variant="primary">
                        Create Client
                    </x-shared::button>
                    <a href="{{ route('client::index') }}">
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
