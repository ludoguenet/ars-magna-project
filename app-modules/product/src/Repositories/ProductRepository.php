<?php

namespace AppModules\Product\src\Repositories;

use AppModules\Product\src\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    /**
     * Get all products.
     */
    public function all(): Collection
    {
        return Product::query()->orderBy('name')->get();
    }

    /**
     * Get active products only.
     */
    public function active(): Collection
    {
        return Product::active()->orderBy('name')->get();
    }

    /**
     * Find a product by ID.
     */
    public function find(int $id): ?Product
    {
        return Product::find($id);
    }

    /**
     * Create a new product.
     */
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    /**
     * Update a product.
     */
    public function update(Product $product, array $data): bool
    {
        return $product->update($data);
    }

    /**
     * Delete a product.
     */
    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    /**
     * Search products by name or SKU.
     */
    public function search(string $query): Collection
    {
        return Product::where('name', 'like', "%{$query}%")
            ->orWhere('sku', 'like', "%{$query}%")
            ->orderBy('name')
            ->get();
    }
}
