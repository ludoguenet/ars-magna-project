@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Invoices</h1>
            <p class="text-sm text-[hsl(var(--color-muted-foreground))] mt-1">Manage and track your invoices</p>
        </div>
        <a href="{{ route('invoice::create') }}">
            <x-shared::button variant="primary">
                New Invoice
            </x-shared::button>
        </a>
    </div>

    <x-shared::card>
        <div class="overflow-x-auto">
            <table class="w-full caption-bottom text-sm">
                <thead>
                    <tr class="border-b border-[hsl(var(--color-border))] transition-colors hover:bg-[hsl(var(--color-muted))]/50">
                        <th class="h-12 px-4 text-left align-middle font-medium text-[hsl(var(--color-muted-foreground))]">
                            Number
                        </th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-[hsl(var(--color-muted-foreground))]">
                            Client
                        </th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-[hsl(var(--color-muted-foreground))]">
                            Issue Date
                        </th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-[hsl(var(--color-muted-foreground))]">
                            Amount
                        </th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-[hsl(var(--color-muted-foreground))]">
                            Status
                        </th>
                        <th class="h-12 px-4 text-right align-middle font-medium text-[hsl(var(--color-muted-foreground))]">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr class="border-b border-[hsl(var(--color-border))] transition-colors hover:bg-[hsl(var(--color-muted))]/50">
                            <td class="p-4 align-middle font-medium">
                                {{ $invoice->invoiceNumber }}
                            </td>
                            <td class="p-4 align-middle text-[hsl(var(--color-muted-foreground))]">
                                {{ $invoice->client?->name ?? '-' }}
                            </td>
                            <td class="p-4 align-middle text-[hsl(var(--color-muted-foreground))]">
                                {{ $invoice->issuedAt?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="p-4 align-middle font-medium">
                                {{ number_format($invoice->total, 2, ',', ' ') }} €
                            </td>
                            <td class="p-4 align-middle">
                                <x-invoice::invoice-status :status="$invoice->status" />
                            </td>
                            <td class="p-4 align-middle text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('invoice::show', $invoice->id) }}" class="text-sm font-medium text-[hsl(var(--color-primary))] hover:underline">
                                        View
                                    </a>
                                    <span class="text-[hsl(var(--color-border))]">|</span>
                                    <a href="{{ route('invoice::edit', $invoice->id) }}" class="text-sm font-medium text-[hsl(var(--color-primary))] hover:underline">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-sm text-[hsl(var(--color-muted-foreground))]">
                                No invoices found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-shared::card>
</div>
@endsection
