<?php

declare(strict_types=1);

namespace AppModules\Product\src\Repositories;

use AppModules\Product\src\Contracts\ProductRepositoryContract;
use AppModules\Product\src\DataTransferObjects\ProductDTO;
use AppModules\Product\src\Models\Product;

class ProductRepository implements ProductRepositoryContract
{
    /**
     * Get all products.
     *
     * @return array<ProductDTO>
     */
    public function all(): array
    {
        return Product::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Product $product) => ProductDTO::fromModel($product))
            ->toArray();
    }

    /**
     * Get active products only.
     *
     * @return array<ProductDTO>
     */
    public function active(): array
    {
        return Product::active()
            ->orderBy('name')
            ->get()
            ->map(fn (Product $product) => ProductDTO::fromModel($product))
            ->toArray();
    }

    /**
     * Find a product by ID.
     */
    public function find(int $id): ?ProductDTO
    {
        $product = Product::find($id);

        return $product ? ProductDTO::fromModel($product) : null;
    }

    /**
     * Search products by name or SKU.
     *
     * @return array<ProductDTO>
     */
    public function search(string $query): array
    {
        return Product::where('name', 'like', "%{$query}%")
            ->orWhere('sku', 'like', "%{$query}%")
            ->orderBy('name')
            ->get()
            ->map(fn (Product $product) => ProductDTO::fromModel($product))
            ->toArray();
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
     * Find a product model by ID (internal use only).
     */
    public function findModel(int $id): ?Product
    {
        return Product::find($id);
    }

    /**
     * Get all product models (internal use only).
     */
    public function allModels(): \Illuminate\Database\Eloquent\Collection
    {
        return Product::query()->orderBy('name')->get();
    }
}
