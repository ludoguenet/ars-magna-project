@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold tracking-tight">New Invoice</h1>
            <p class="text-sm text-[hsl(var(--color-muted-foreground))] mt-1">Create a new invoice for your client</p>
        </div>

        <x-shared::card>
            <form action="{{ route('invoice::store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="client_id" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                            Client <span class="text-[hsl(var(--color-destructive))]">*</span>
                        </label>
                        <select
                            name="client_id"
                            id="client_id"
                            required
                            class="flex h-10 w-full rounded-lg border border-[hsl(var(--color-input))] bg-[hsl(var(--color-background))] px-3 py-2 text-sm ring-offset-[hsl(var(--color-background))] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[hsl(var(--color-ring))] focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <option value="">Select a client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <p class="text-sm font-medium text-[hsl(var(--color-destructive))]">{{ $message }}</p>
                        @enderror
                    </div>

                    <x-shared::input
                        type="date"
                        name="issued_at"
                        label="Issue Date"
                        :value="old('issued_at', now()->format('Y-m-d'))"
                        required
                        :error="isset($errors) ? $errors->first('issued_at') : null"
                    />

                    <x-shared::input
                        type="date"
                        name="due_at"
                        label="Due Date"
                        :value="old('due_at')"
                        :error="isset($errors) ? $errors->first('due_at') : null"
                    />
                </div>

                <div class="space-y-4">
                    <div>
                        <h2 class="text-lg font-semibold leading-none tracking-tight mb-4">Items</h2>
                        <div id="items-container" class="space-y-4">
                            <div class="item-row rounded-lg border border-[hsl(var(--color-border))] p-4" data-index="0">
                                <div class="grid grid-cols-12 gap-4">
                                    <div class="col-span-12 mb-2">
                                        <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                            Product (Optional)
                                        </label>
                                        <select
                                            name="items[0][product_id]"
                                            class="product-select flex h-10 w-full rounded-lg border border-[hsl(var(--color-input))] bg-[hsl(var(--color-background))] px-3 py-2 text-sm ring-offset-[hsl(var(--color-background))] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[hsl(var(--color-ring))] focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            <option value="">-- Select a product or enter custom description --</option>
                                            @foreach($products as $product)
                                                <option 
                                                    value="{{ $product->id }}"
                                                    data-description="{{ $product->name }}"
                                                    data-price="{{ $product->price }}"
                                                    data-tax-rate="{{ $product->taxRate }}"
                                                >
                                                    {{ $product->name }} - {{ number_format($product->price, 2, ',', ' ') }} €
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-5">
                                        <x-shared::input
                                            name="items[0][description]"
                                            label="Description"
                                            required
                                            :error="isset($errors) ? $errors->first('items.0.description') : null"
                                        />
                                    </div>
                                    <div class="col-span-2">
                                        <x-shared::input
                                            type="number"
                                            step="0.01"
                                            name="items[0][quantity]"
                                            label="Quantity"
                                            value="1"
                                            required
                                            :error="isset($errors) ? $errors->first('items.0.quantity') : null"
                                        />
                                    </div>
                                    <div class="col-span-2">
                                        <x-shared::input
                                            type="number"
                                            step="0.01"
                                            name="items[0][unit_price]"
                                            label="Unit Price"
                                            required
                                            :error="isset($errors) ? $errors->first('items.0.unit_price') : null"
                                        />
                                    </div>
                                    <div class="col-span-2">
                                        <x-shared::input
                                            type="number"
                                            step="0.01"
                                            name="items[0][tax_rate]"
                                            label="Tax Rate (%)"
                                            value="0"
                                            :error="isset($errors) ? $errors->first('items.0.tax_rate') : null"
                                        />
                                    </div>
                                    <div class="col-span-1 flex items-end">
                                        <button type="button" class="remove-item text-sm font-medium text-[hsl(var(--color-destructive))] hover:underline" style="display: none;">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add-item" class="mt-2 text-sm font-medium text-[hsl(var(--color-primary))] hover:underline">
                            + Add Item
                        </button>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="notes" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                        Notes
                    </label>
                    <textarea
                        name="notes"
                        id="notes"
                        rows="4"
                        class="flex min-h-[80px] w-full rounded-lg border border-[hsl(var(--color-input))] bg-[hsl(var(--color-background))] px-3 py-2 text-sm ring-offset-[hsl(var(--color-background))] placeholder:text-[hsl(var(--color-muted-foreground))] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[hsl(var(--color-ring))] focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-sm font-medium text-[hsl(var(--color-destructive))]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="terms" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                        Terms
                    </label>
                    <textarea
                        name="terms"
                        id="terms"
                        rows="4"
                        class="flex min-h-[80px] w-full rounded-lg border border-[hsl(var(--color-input))] bg-[hsl(var(--color-background))] px-3 py-2 text-sm ring-offset-[hsl(var(--color-background))] placeholder:text-[hsl(var(--color-muted-foreground))] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[hsl(var(--color-ring))] focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >{{ old('terms') }}</textarea>
                    @error('terms')
                        <p class="text-sm font-medium text-[hsl(var(--color-destructive))]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <x-shared::button type="submit" variant="primary">
                        Create Invoice
                    </x-shared::button>
                    <a href="{{ route('invoice::index') }}">
                        <x-shared::button variant="outline" type="button">
                            Cancel
                        </x-shared::button>
                    </a>
                </div>
            </form>
        </x-shared::card>
    </div>
</div>

<script>
    let itemIndex = 1;
    
    // Handle product selection
    function handleProductSelect(selectElement) {
        const row = selectElement.closest('.item-row');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        
        if (selectedOption.value) {
            const description = selectedOption.dataset.description;
            const price = selectedOption.dataset.price;
            const taxRate = selectedOption.dataset.taxRate;
            
            // Fill in the fields
            row.querySelector('input[name*="[description]"]').value = description;
            row.querySelector('input[name*="[unit_price]"]').value = price;
            row.querySelector('input[name*="[tax_rate]"]').value = taxRate;
        }
    }
    
    // Add event listeners to existing product selects
    document.querySelectorAll('.product-select').forEach(select => {
        select.addEventListener('change', function() {
            handleProductSelect(this);
        });
    });
    
    document.getElementById('add-item').addEventListener('click', function() {
        const container = document.getElementById('items-container');
        const newItem = container.querySelector('.item-row').cloneNode(true);
        
        newItem.setAttribute('data-index', itemIndex);
        
        // Update select names
        newItem.querySelectorAll('select').forEach(select => {
            const name = select.getAttribute('name');
            if (name) {
                select.setAttribute('name', name.replace(/\[0\]/, `[${itemIndex}]`));
                select.selectedIndex = 0;
            }
        });
        
        // Update input names and clear values
        newItem.querySelectorAll('input').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/\[0\]/, `[${itemIndex}]`));
                if (name.includes('quantity')) {
                    input.value = '1';
                } else if (name.includes('tax_rate')) {
                    input.value = '0';
                } else {
                    input.value = '';
                }
            }
        });
        
        // Add product select event listener
        const productSelect = newItem.querySelector('.product-select');
        productSelect.addEventListener('change', function() {
            handleProductSelect(this);
        });
        
        // Show remove button
        newItem.querySelector('.remove-item').style.display = 'block';
        newItem.querySelector('.remove-item').addEventListener('click', function() {
            newItem.remove();
        });
        
        container.appendChild(newItem);
        itemIndex++;
    });
    
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.item-row').remove();
        });
    });
</script>
@endsection
