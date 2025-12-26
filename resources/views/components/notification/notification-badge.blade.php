@props(['count' => 0])

@if($count > 0)
    <span class="inline-flex items-center rounded-full bg-[hsl(var(--color-destructive))] px-2 py-0.5 text-xs font-medium text-[hsl(var(--color-destructive-foreground))]">
        {{ $count }}
    </span>
@endif
