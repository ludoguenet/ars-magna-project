@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Facture {{ $invoice->invoice_number }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('invoice::edit', $invoice->id) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Modifier
                </a>
                <form action="{{ route('invoice::destroy', $invoice->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette facture ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>

        <x-shared::card>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Client</h3>
                    <p class="text-sm text-gray-900">{{ $invoice->client->name ?? '-' }}</p>
                    @if($invoice->client)
                        <p class="text-sm text-gray-500 mt-1">{{ $invoice->client->company ?? '' }}</p>
                    @endif
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Dates</h3>
                    <p class="text-sm text-gray-900">
                        Émission: {{ $invoice->issued_at?->format('d/m/Y') ?? '-' }}
                    </p>
                    @if($invoice->due_at)
                        <p class="text-sm text-gray-900">
                            Échéance: {{ $invoice->due_at->format('d/m/Y') }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Statut</h3>
                <x-invoice::invoice-status :status="$invoice->status" />
            </div>

            <div class="overflow-x-auto mb-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix unitaire</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">TVA</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($invoice->items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->description }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($item->quantity, 2, ',', ' ') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($item->unit_price, 2, ',', ' ') }} €
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($item->tax_rate, 2, ',', ' ') }} %
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    {{ number_format($item->line_total, 2, ',', ' ') }} €
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Aucun article
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                                Sous-total:
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                {{ number_format($invoice->subtotal, 2, ',', ' ') }} €
                            </td>
                        </tr>
                        @if($invoice->discount_amount > 0)
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                                    Remise:
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                    -{{ number_format($invoice->discount_amount, 2, ',', ' ') }} €
                                </td>
                            </tr>
                        @endif
                        @if($invoice->tax_amount > 0)
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                                    TVA:
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                    {{ number_format($invoice->tax_amount, 2, ',', ' ') }} €
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-right text-lg font-bold text-gray-900">
                                Total:
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-lg font-bold text-gray-900 text-right">
                                {{ number_format($invoice->total, 2, ',', ' ') }} €
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if($invoice->notes)
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Notes</h3>
                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $invoice->notes }}</p>
                </div>
            @endif

            @if($invoice->terms)
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Conditions</h3>
                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $invoice->terms }}</p>
                </div>
            @endif
        </x-shared::card>
    </div>
</div>
@endsection
