@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Products</h1>
            <p class="text-sm text-[hsl(var(--color-muted-foreground))] mt-1">Manage your product catalog</p>
        </div>
        <a href="{{ route('product::create') }}">
            <x-shared::button variant="primary">
                New Product
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
                            SKU
                        </th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-[hsl(var(--color-muted-foreground))]">
                            Price
                        </th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-[hsl(var(--color-muted-foreground))]">
                            Tax Rate
                        </th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-[hsl(var(--color-muted-foreground))]">
                            Unit
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
                    @forelse($products as $product)
                        <tr class="border-b border-[hsl(var(--color-border))] transition-colors hover:bg-[hsl(var(--color-muted))]/50">
                            <td class="p-4 align-middle font-medium">
                                {{ $product->name }}
                            </td>
                            <td class="p-4 align-middle text-[hsl(var(--color-muted-foreground))]">
                                {{ $product->sku ?? '-' }}
                            </td>
                            <td class="p-4 align-middle font-medium">
                                {{ number_format($product->price, 2, ',', ' ') }} €
                            </td>
                            <td class="p-4 align-middle text-[hsl(var(--color-muted-foreground))]">
                                {{ $product->tax_rate ? number_format($product->tax_rate, 2, ',', ' ') . '%' : '-' }}
                            </td>
                            <td class="p-4 align-middle text-[hsl(var(--color-muted-foreground))]">
                                {{ $product->unit ?? '-' }}
                            </td>
                            <td class="p-4 align-middle">
                                @if($product->is_active)
                                    <span class="inline-flex items-center rounded-full border border-green-200 bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-900">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full border border-zinc-200 bg-zinc-100 px-2.5 py-0.5 text-xs font-semibold text-zinc-900">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 align-middle text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('product::show', $product->id) }}" class="text-sm font-medium text-[hsl(var(--color-primary))] hover:underline">
                                        View
                                    </a>
                                    <span class="text-[hsl(var(--color-border))]">|</span>
                                    <a href="{{ route('product::edit', $product->id) }}" class="text-sm font-medium text-[hsl(var(--color-primary))] hover:underline">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-sm text-[hsl(var(--color-muted-foreground))]">
                                No products found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-shared::card>
</div>
@endsection
