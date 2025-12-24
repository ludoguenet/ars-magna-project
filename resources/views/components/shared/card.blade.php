@props(['title' => null])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-[hsl(var(--color-border))] bg-[hsl(var(--color-card))] text-[hsl(var(--color-card-foreground))] shadow-sm']) }}>
    @if($title)
        <div class="px-6 py-4 border-b border-[hsl(var(--color-border))]">
            <h3 class="text-lg font-semibold leading-none tracking-tight">{{ $title }}</h3>
        </div>
    @endif
    <div class="p-6">
        {{ $slot }}
    </div>
</div>
