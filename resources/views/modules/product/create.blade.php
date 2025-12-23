@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Nouveau produit</h1>

        <x-shared::card>
            <form action="{{ route('product::store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-shared::input
                        name="name"
                        label="Nom"
                        required
                    />

                    <x-shared::input
                        name="sku"
                        label="SKU"
                    />

                    <x-shared::input
                        type="number"
                        name="price"
                        label="Prix"
                        step="0.01"
                        min="0"
                        required
                    />

                    <x-shared::input
                        type="number"
                        name="tax_rate"
                        label="Taux TVA (%)"
                        step="0.01"
                        min="0"
                        max="100"
                    />

                    <x-shared::input
                        name="unit"
                        label="Unité"
                        placeholder="ex: pièce, kg, m²"
                    />
                </div>

                <div class="mt-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description
                    </label>
                    <textarea
                        name="description"
                        id="description"
                        rows="4"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    >{{ old('description') }}</textarea>
                    @if(isset($errors) && $errors->has('description'))
                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('description') }}</p>
                    @endif
                </div>

                <div class="mt-4">
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                        <span class="ml-2 text-sm text-gray-700">Produit actif</span>
                    </label>
                    @if(isset($errors) && $errors->has('is_active'))
                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('is_active') }}</p>
                    @endif
                </div>

                <div class="mt-6 flex gap-4">
                    <x-shared::button type="submit" variant="primary">
                        Créer
                    </x-shared::button>
                    <a href="{{ route('product::index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Annuler
                    </a>
                </div>
            </form>
        </x-shared::card>
    </div>
</div>
@endsection
