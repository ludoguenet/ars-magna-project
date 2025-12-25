@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit Client</h1>

        <x-shared::card>
            <form action="{{ route('client::update', $client->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-shared::input
                        name="name"
                        label="Name"
                        :value="old('name', $client->name)"
                        required
                        :error="isset($errors) ? $errors->first('name') : null"
                    />

                    <x-shared::input
                        name="company"
                        label="Company"
                        :value="old('company', $client->company)"
                        :error="isset($errors) ? $errors->first('company') : null"
                    />

                    <x-shared::input
                        type="email"
                        name="email"
                        label="Email"
                        :value="old('email', $client->email)"
                        :error="isset($errors) ? $errors->first('email') : null"
                    />

                    <x-shared::input
                        type="tel"
                        name="phone"
                        label="Phone"
                        :value="old('phone', $client->phone)"
                        :error="isset($errors) ? $errors->first('phone') : null"
                    />

                    <x-shared::input
                        name="vat_number"
                        label="VAT Number"
                        :value="old('vat_number', $client->vat_number)"
                        :error="isset($errors) ? $errors->first('vat_number') : null"
                    />
                </div>

                <div class="mt-4">
                    <x-shared::input
                        name="address"
                        label="Address"
                        :value="old('address', $client->address)"
                        :error="isset($errors) ? $errors->first('address') : null"
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <x-shared::input
                        name="postal_code"
                        label="Postal Code"
                        :value="old('postal_code', $client->postal_code)"
                        :error="isset($errors) ? $errors->first('postal_code') : null"
                    />

                    <x-shared::input
                        name="city"
                        label="City"
                        :value="old('city', $client->city)"
                        :error="isset($errors) ? $errors->first('city') : null"
                    />

                    <x-shared::input
                        name="country"
                        label="Country"
                        :value="old('country', $client->country)"
                        :error="isset($errors) ? $errors->first('country') : null"
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
                    >{{ old('notes', $client->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6 flex gap-4">
                    <x-shared::button type="submit" variant="primary">
                        Save
                    </x-shared::button>
                    <a href="{{ route('client::show', $client->id) }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                </div>
            </form>
        </x-shared::card>
    </div>
</div>
@endsection
