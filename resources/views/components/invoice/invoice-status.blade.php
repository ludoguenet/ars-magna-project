@props(['status'])

@php
$colors = [
    'draft' => 'bg-zinc-100 text-zinc-900 border-zinc-200',
    'sent' => 'bg-blue-100 text-blue-900 border-blue-200',
    'paid' => 'bg-green-100 text-green-900 border-green-200',
    'overdue' => 'bg-red-100 text-red-900 border-red-200',
    'cancelled' => 'bg-zinc-100 text-zinc-900 border-zinc-200',
];
$statusValue = $status instanceof \AppModules\Invoice\src\Enums\InvoiceStatus ? $status->value : $status;
$color = $colors[$statusValue] ?? $colors['draft'];
$label = $status instanceof \AppModules\Invoice\src\Enums\InvoiceStatus ? $status->label() : ucfirst($statusValue);
@endphp

<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors {{ $color }}">
    {{ $label }}
</span>
