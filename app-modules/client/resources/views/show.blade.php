@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ $client->name }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('client::edit', $client->id) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Edit
                </a>
                <form action="{{ route('client::destroy', $client->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this client?');">
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
                        <dd class="mt-1 text-sm text-gray-900">{{ $client->name }}</dd>
                    </div>
                    @if($client->company)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Company</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $client->company }}</dd>
                        </div>
                    @endif
                    @if($client->email)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="mailto:{{ $client->email }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $client->email }}
                                </a>
                            </dd>
                        </div>
                    @endif
                    @if($client->phone)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="tel:{{ $client->phone }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $client->phone }}
                                </a>
                            </dd>
                        </div>
                    @endif
                    @if($client->vat_number)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">VAT Number</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $client->vat_number }}</dd>
                        </div>
                    @endif
                </dl>
            </x-shared::card>

            <x-shared::card title="Address">
                @if($client->full_address)
                    <p class="text-sm text-gray-900">{{ $client->full_address }}</p>
                @else
                    <p class="text-sm text-gray-500">No address provided</p>
                @endif
            </x-shared::card>
        </div>

        @if($client->notes)
            <x-shared::card title="Notes" class="mt-6">
                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $client->notes }}</p>
            </x-shared::card>
        @endif

        <x-shared::card title="Invoices" class="mt-6">
            @if($client->invoices->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($client->invoices as $invoice)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $invoice->invoice_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $invoice->issued_at?->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($invoice->total, 2, ',', ' ') }} €
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-invoice::invoice-status :status="$invoice->status" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('invoice::show', $invoice->id) }}" class="text-blue-600 hover:text-blue-900">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500">No invoices for this client</p>
            @endif
        </x-shared::card>
    </div>
</div>
@endsection
