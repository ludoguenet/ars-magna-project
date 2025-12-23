@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Nouvelle facture</h1>

        <x-shared::card>
            <form action="{{ route('invoice::store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Client <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="client_id"
                            id="client_id"
                            required
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        >
                            <option value="">Sélectionner un client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <x-shared::input
                        type="date"
                        name="issued_at"
                        label="Date d'émission"
                        :value="old('issued_at', now()->format('Y-m-d'))"
                        required
                        :error="isset($errors) ? $errors->first('issued_at') : null"
                    />

                    <x-shared::input
                        type="date"
                        name="due_at"
                        label="Date d'échéance"
                        :value="old('due_at')"
                        :error="isset($errors) ? $errors->first('due_at') : null"
                    />
                </div>

                <div class="mt-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Articles</h2>
                    <div id="items-container">
                        <div class="item-row border-b border-gray-200 pb-4 mb-4">
                            <div class="grid grid-cols-12 gap-4">
                                <div class="col-span-5">
                                    <x-shared::input
                                        name="items[0][description]"
                                        label="Description"
                                        required
                                        :error="isset($errors) ? $errors->first('items.0.description') : null"
                                    />
                                </div>
                                <div class="col-span-2">
                                    <x-shared::input
                                        type="number"
                                        step="0.01"
                                        name="items[0][quantity]"
                                        label="Quantité"
                                        value="1"
                                        required
                                        :error="isset($errors) ? $errors->first('items.0.quantity') : null"
                                    />
                                </div>
                                <div class="col-span-2">
                                    <x-shared::input
                                        type="number"
                                        step="0.01"
                                        name="items[0][unit_price]"
                                        label="Prix unitaire"
                                        required
                                        :error="isset($errors) ? $errors->first('items.0.unit_price') : null"
                                    />
                                </div>
                                <div class="col-span-2">
                                    <x-shared::input
                                        type="number"
                                        step="0.01"
                                        name="items[0][tax_rate]"
                                        label="Taux TVA (%)"
                                        value="0"
                                        :error="isset($errors) ? $errors->first('items.0.tax_rate') : null"
                                    />
                                </div>
                                <div class="col-span-1 flex items-end">
                                    <button type="button" class="remove-item text-red-600 hover:text-red-800" style="display: none;">
                                        Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="add-item" class="mt-2 text-blue-600 hover:text-blue-800">
                        + Ajouter un article
                    </button>
                </div>

                <div class="mt-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                        Notes
                    </label>
                    <textarea
                        name="notes"
                        id="notes"
                        rows="4"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    >{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6">
                    <label for="terms" class="block text-sm font-medium text-gray-700 mb-1">
                        Conditions
                    </label>
                    <textarea
                        name="terms"
                        id="terms"
                        rows="4"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    >{{ old('terms') }}</textarea>
                    @error('terms')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6 flex gap-4">
                    <x-shared::button type="submit" variant="primary">
                        Créer
                    </x-shared::button>
                    <a href="{{ route('invoice::index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Annuler
                    </a>
                </div>
            </form>
        </x-shared::card>
    </div>
</div>

<script>
    let itemIndex = 1;
    document.getElementById('add-item').addEventListener('click', function() {
        const container = document.getElementById('items-container');
        const newItem = container.querySelector('.item-row').cloneNode(true);
        
        // Update input names
        newItem.querySelectorAll('input').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/\[0\]/, `[${itemIndex}]`));
                input.value = '';
            }
        });
        
        // Show remove button
        newItem.querySelector('.remove-item').style.display = 'block';
        newItem.querySelector('.remove-item').addEventListener('click', function() {
            newItem.remove();
        });
        
        container.appendChild(newItem);
        itemIndex++;
    });
    
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.item-row').remove();
        });
    });
</script>
@endsection
