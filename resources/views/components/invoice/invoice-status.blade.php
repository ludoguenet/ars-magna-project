@props(['status'])

@php
$colors = [
    'draft' => 'bg-gray-100 text-gray-800',
    'sent' => 'bg-blue-100 text-blue-800',
    'paid' => 'bg-green-100 text-green-800',
    'overdue' => 'bg-red-100 text-red-800',
    'cancelled' => 'bg-gray-100 text-gray-800',
];
$statusValue = $status instanceof \AppModules\Invoice\src\Enums\InvoiceStatus ? $status->value : $status;
$color = $colors[$statusValue] ?? $colors['draft'];
$label = $status instanceof \AppModules\Invoice\src\Enums\InvoiceStatus ? $status->label() : ucfirst($statusValue);
@endphp

<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
    {{ $label }}
</span>
