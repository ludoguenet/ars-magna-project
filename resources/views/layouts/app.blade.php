<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-[hsl(var(--color-background))]">
        @auth
        <!-- Navigation -->
        <nav class="sticky top-0 z-50 w-full border-b border-[hsl(var(--color-border))] bg-[hsl(var(--color-background))]/95 backdrop-blur supports-[backdrop-filter]:bg-[hsl(var(--color-background))]/60">
            <div class="container flex h-16 items-center px-4">
                <div class="mr-4 flex">
                    <a href="{{ route('dashboard') }}" class="mr-6 flex items-center space-x-2">
                        <span class="text-xl font-bold">{{ config('app.name', 'Facturation') }}</span>
                    </a>
                    <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
                        <a href="{{ route('dashboard') }}" class="transition-colors hover:text-[hsl(var(--color-foreground))] {{ request()->routeIs('dashboard') ? 'text-[hsl(var(--color-foreground))]' : 'text-[hsl(var(--color-muted-foreground))]' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('invoice::index') }}" class="transition-colors hover:text-[hsl(var(--color-foreground))] {{ request()->routeIs('invoice::*') ? 'text-[hsl(var(--color-foreground))]' : 'text-[hsl(var(--color-muted-foreground))]' }}">
                            Invoices
                        </a>
                        <a href="{{ route('client::index') }}" class="transition-colors hover:text-[hsl(var(--color-foreground))] {{ request()->routeIs('client::*') ? 'text-[hsl(var(--color-foreground))]' : 'text-[hsl(var(--color-muted-foreground))]' }}">
                            Clients
                        </a>
                        <a href="{{ route('product::index') }}" class="transition-colors hover:text-[hsl(var(--color-foreground))] {{ request()->routeIs('product::*') ? 'text-[hsl(var(--color-foreground))]' : 'text-[hsl(var(--color-muted-foreground))]' }}">
                            Products
                        </a>
                    </nav>
                </div>
                <div class="flex flex-1 items-center justify-between space-x-2 md:justify-end">
                    <div class="hidden md:flex items-center gap-4">
                        <span class="text-sm text-[hsl(var(--color-muted-foreground))]">{{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <x-shared::button variant="ghost" type="submit" class="text-sm">
                                Logout
                            </x-shared::button>
                        </form>
                    </div>
                    <button @click="sidebarOpen = !sidebarOpen" class="inline-flex items-center justify-center rounded-lg p-2 text-[hsl(var(--color-muted-foreground))] transition-colors hover:bg-[hsl(var(--color-accent))] hover:text-[hsl(var(--color-foreground))] md:hidden">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <div x-show="sidebarOpen" x-transition class="border-t border-[hsl(var(--color-border))] md:hidden">
                <div class="space-y-1 px-2 pb-3 pt-2">
                    <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 text-base font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-[hsl(var(--color-accent))] text-[hsl(var(--color-foreground))]' : 'text-[hsl(var(--color-muted-foreground))] hover:bg-[hsl(var(--color-accent))] hover:text-[hsl(var(--color-foreground))]' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('invoice::index') }}" class="block rounded-lg px-3 py-2 text-base font-medium transition-colors {{ request()->routeIs('invoice::*') ? 'bg-[hsl(var(--color-accent))] text-[hsl(var(--color-foreground))]' : 'text-[hsl(var(--color-muted-foreground))] hover:bg-[hsl(var(--color-accent))] hover:text-[hsl(var(--color-foreground))]' }}">
                        Invoices
                    </a>
                    <a href="{{ route('client::index') }}" class="block rounded-lg px-3 py-2 text-base font-medium transition-colors {{ request()->routeIs('client::*') ? 'bg-[hsl(var(--color-accent))] text-[hsl(var(--color-foreground))]' : 'text-[hsl(var(--color-muted-foreground))] hover:bg-[hsl(var(--color-accent))] hover:text-[hsl(var(--color-foreground))]' }}">
                        Clients
                    </a>
                    <a href="{{ route('product::index') }}" class="block rounded-lg px-3 py-2 text-base font-medium transition-colors {{ request()->routeIs('product::*') ? 'bg-[hsl(var(--color-accent))] text-[hsl(var(--color-foreground))]' : 'text-[hsl(var(--color-muted-foreground))] hover:bg-[hsl(var(--color-accent))] hover:text-[hsl(var(--color-foreground))]' }}">
                        Products
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="px-3 py-2">
                        @csrf
                        <button type="submit" class="w-full text-left text-base font-medium text-[hsl(var(--color-muted-foreground))] hover:bg-[hsl(var(--color-accent))] hover:text-[hsl(var(--color-foreground))] rounded-lg px-3 py-2 transition-colors">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>
        @endauth

        <!-- Page Content -->
        <main class="flex-1">
            @if(session('success'))
                <div class="container mx-auto px-4 py-4">
                    <x-shared::alert type="success" dismissible>
                        {{ session('success') }}
                    </x-shared::alert>
                </div>
            @endif

            @if(session('error'))
                <div class="container mx-auto px-4 py-4">
                    <x-shared::alert type="error" dismissible>
                        {{ session('error') }}
                    </x-shared::alert>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
