@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('product::edit', $product->id) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Edit
                </a>
                <form action="{{ route('product::destroy', $product->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-shared::card title="Information">
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->name }}</dd>
                    </div>
                    @if($product->sku)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">SKU</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->sku }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Price</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($product->price, 2, ',', ' ') }} €</dd>
                    </div>
                    @if($product->tax_rate)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tax Rate</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($product->tax_rate, 2, ',', ' ') }}%</dd>
                        </div>
                    @endif
                    @if($product->unit)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Unit</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->unit }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            @if($product->is_active)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Inactive
                                </span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </x-shared::card>

            @if($product->description)
                <x-shared::card title="Description">
                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $product->description }}</p>
                </x-shared::card>
            @endif
        </div>
    </div>
</div>
@endsection
