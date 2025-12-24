@props(['type' => 'text', 'label' => null, 'name' => null, 'value' => null, 'required' => false, 'error' => null])

<div class="space-y-2">
    @if($label)
        <label for="{{ $name }}" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
            {{ $label }}
            @if($required)
                <span class="text-[hsl(var(--color-destructive))]">*</span>
            @endif
        </label>
    @endif
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'flex h-10 w-full rounded-lg border border-[hsl(var(--color-input))] bg-[hsl(var(--color-background))] px-3 py-2 text-sm ring-offset-[hsl(var(--color-background))] file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-[hsl(var(--color-muted-foreground))] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[hsl(var(--color-ring))] focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50']) }}
    >
    @if($error)
        <p class="text-sm font-medium text-[hsl(var(--color-destructive))]">{{ $error }}</p>
    @endif
    @error($name)
        <p class="text-sm font-medium text-[hsl(var(--color-destructive))]">{{ $message }}</p>
    @enderror
</div>
