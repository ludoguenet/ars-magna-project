@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('product::edit', $product->id) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Modifier
                </a>
                <form action="{{ route('product::destroy', $product->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-shared::card title="Informations">
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nom</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->name }}</dd>
                    </div>
                    @if($product->sku)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">SKU</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->sku }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Prix</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($product->price, 2, ',', ' ') }} €</dd>
                    </div>
                    @if($product->tax_rate)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Taux TVA</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($product->tax_rate, 2, ',', ' ') }}%</dd>
                        </div>
                    @endif
                    @if($product->unit)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Unité</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->unit }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Statut</dt>
                        <dd class="mt-1">
                            @if($product->is_active)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Actif
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Inactif
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
