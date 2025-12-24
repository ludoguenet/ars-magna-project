@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
        <p class="text-sm text-[hsl(var(--color-muted-foreground))] mt-1">Overview of your business metrics</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Total Clients -->
        <x-shared::card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[hsl(var(--color-muted-foreground))]">Total Clients</p>
                    <p class="text-2xl font-bold mt-1">{{ $stats['total_clients'] }}</p>
                </div>
                <div class="rounded-full bg-blue-100 p-3">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </x-shared::card>

        <!-- Total Products -->
        <x-shared::card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[hsl(var(--color-muted-foreground))]">Total Products</p>
                    <p class="text-2xl font-bold mt-1">{{ $stats['total_products'] }}</p>
                </div>
                <div class="rounded-full bg-green-100 p-3">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </x-shared::card>

        <!-- Total Invoices -->
        <x-shared::card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[hsl(var(--color-muted-foreground))]">Total Invoices</p>
                    <p class="text-2xl font-bold mt-1">{{ $stats['total_invoices'] }}</p>
                </div>
                <div class="rounded-full bg-purple-100 p-3">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </x-shared::card>

        <!-- Total Revenue -->
        <x-shared::card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[hsl(var(--color-muted-foreground))]">Total Revenue</p>
                    <p class="text-2xl font-bold mt-1">€{{ number_format($stats['total_revenue'], 2) }}</p>
                </div>
                <div class="rounded-full bg-yellow-100 p-3">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </x-shared::card>

        <!-- Pending Invoices -->
        <x-shared::card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[hsl(var(--color-muted-foreground))]">Pending Invoices</p>
                    <p class="text-2xl font-bold mt-1">{{ $stats['pending_invoices'] }}</p>
                </div>
                <div class="rounded-full bg-orange-100 p-3">
                    <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </x-shared::card>

        <!-- Overdue Invoices -->
        <x-shared::card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[hsl(var(--color-muted-foreground))]">Overdue Invoices</p>
                    <p class="text-2xl font-bold mt-1 text-[hsl(var(--color-destructive))]">{{ $stats['overdue_invoices'] }}</p>
                </div>
                <div class="rounded-full bg-red-100 p-3">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </x-shared::card>
    </div>

    <!-- Recent Invoices -->
    <x-shared::card title="Recent Invoices">
        <div class="overflow-x-auto">
            <table class="w-full caption-bottom text-sm">
                <thead>
                    <tr class="border-b border-[hsl(var(--color-border))] transition-colors hover:bg-[hsl(var(--color-muted))]/50">
                        <th class="h-12 px-4 text-left align-middle font-medium text-[hsl(var(--color-muted-foreground))]">Invoice #</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-[hsl(var(--color-muted-foreground))]">Client</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-[hsl(var(--color-muted-foreground))]">Status</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-[hsl(var(--color-muted-foreground))]">Total</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-[hsl(var(--color-muted-foreground))]">Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentInvoices as $invoice)
                    <tr class="border-b border-[hsl(var(--color-border))] transition-colors hover:bg-[hsl(var(--color-muted))]/50">
                        <td class="p-4 align-middle font-medium">
                            {{ $invoice->invoiceNumber }}
                        </td>
                        <td class="p-4 align-middle text-[hsl(var(--color-muted-foreground))]">
                            {{ $invoice->client?->name ?? '-' }}
                        </td>
                        <td class="p-4 align-middle">
                            <x-invoice::invoice-status :status="$invoice->status" />
                        </td>
                        <td class="p-4 align-middle font-medium">
                            €{{ number_format($invoice->total, 2) }}
                        </td>
                        <td class="p-4 align-middle text-[hsl(var(--color-muted-foreground))]">
                            {{ $invoice->dueAt ? $invoice->dueAt->format('Y-m-d') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-sm text-[hsl(var(--color-muted-foreground))]">
                            No invoices found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-shared::card>
</div>
@endsection
