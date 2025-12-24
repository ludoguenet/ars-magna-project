@props(['type' => 'button', 'variant' => 'primary'])

@php
$variants = [
    'primary' => 'bg-[hsl(var(--color-primary))] text-[hsl(var(--color-primary-foreground))] hover:opacity-90',
    'secondary' => 'bg-[hsl(var(--color-secondary))] text-[hsl(var(--color-secondary-foreground))] hover:bg-[hsl(var(--color-secondary))]/80 border border-[hsl(var(--color-border))]',
    'destructive' => 'bg-[hsl(var(--color-destructive))] text-[hsl(var(--color-destructive-foreground))] hover:opacity-90',
    'ghost' => 'hover:bg-[hsl(var(--color-accent))] text-[hsl(var(--color-foreground))]',
    'outline' => 'border border-[hsl(var(--color-border))] bg-transparent hover:bg-[hsl(var(--color-accent))] text-[hsl(var(--color-foreground))]',
];
$classes = $variants[$variant] ?? $variants['primary'];
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "{$classes} inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[hsl(var(--color-ring))] focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"]) }}>
    {{ $slot }}
</button>
