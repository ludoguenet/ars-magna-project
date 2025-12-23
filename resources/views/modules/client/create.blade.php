@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Nouveau client</h1>

        <x-shared::card>
            <form action="{{ route('client::store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-shared::input
                        name="name"
                        label="Nom"
                        required
                        :error="$errors->first('name')"
                    />

                    <x-shared::input
                        name="company"
                        label="Entreprise"
                        :error="$errors->first('company')"
                    />

                    <x-shared::input
                        type="email"
                        name="email"
                        label="Email"
                        :error="$errors->first('email')"
                    />

                    <x-shared::input
                        type="tel"
                        name="phone"
                        label="Téléphone"
                        :error="$errors->first('phone')"
                    />

                    <x-shared::input
                        name="vat_number"
                        label="Numéro TVA"
                        :error="$errors->first('vat_number')"
                    />
                </div>

                <div class="mt-4">
                    <x-shared::input
                        name="address"
                        label="Adresse"
                        :error="$errors->first('address')"
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <x-shared::input
                        name="postal_code"
                        label="Code postal"
                        :error="$errors->first('postal_code')"
                    />

                    <x-shared::input
                        name="city"
                        label="Ville"
                        :error="$errors->first('city')"
                    />

                    <x-shared::input
                        name="country"
                        label="Pays"
                        :error="$errors->first('country')"
                    />
                </div>

                <div class="mt-4">
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

                <div class="mt-6 flex gap-4">
                    <x-shared::button type="submit" variant="primary">
                        Créer
                    </x-shared::button>
                    <a href="{{ route('client::index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Annuler
                    </a>
                </div>
            </form>
        </x-shared::card>
    </div>
</div>
@endsection
