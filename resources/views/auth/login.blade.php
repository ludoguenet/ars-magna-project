@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex min-h-[calc(100vh-8rem)] items-center justify-center">
        <div class="w-full max-w-md">
            <x-shared::card>
                <div class="mb-6">
                    <h1 class="text-2xl font-bold tracking-tight">Sign in</h1>
                    <p class="text-sm text-[hsl(var(--color-muted-foreground))] mt-1">
                        Enter your credentials to access your account
                    </p>
                </div>

                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf

                    <x-shared::input
                        type="email"
                        name="email"
                        label="Email"
                        :value="old('email')"
                        required
                        autofocus
                        autocomplete="username"
                    />

                    <x-shared::input
                        type="password"
                        name="password"
                        label="Password"
                        required
                        autocomplete="current-password"
                    />

                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            class="h-4 w-4 rounded border-[hsl(var(--color-input))] text-[hsl(var(--color-primary))] focus:ring-2 focus:ring-[hsl(var(--color-ring))] focus:ring-offset-2"
                        >
                        <label for="remember" class="ml-2 text-sm text-[hsl(var(--color-foreground))]">
                            Remember me
                        </label>
                    </div>

                    <x-shared::button type="submit" variant="primary" class="w-full">
                        Sign in
                    </x-shared::button>
                </form>
            </x-shared::card>
        </div>
    </div>
</div>
@endsection
