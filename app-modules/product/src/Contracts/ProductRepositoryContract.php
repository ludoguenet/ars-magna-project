<?php

declare(strict_types=1);

namespace AppModules\Product\src\Contracts;

use AppModules\Product\src\DataTransferObjects\ProductDTO;
use AppModules\Product\src\Models\Product;

interface ProductRepositoryContract
{
    /**
     * Get all products.
     *
     * @return array<ProductDTO>
     */
    public function all(): array;

    /**
     * Get active products only.
     *
     * @return array<ProductDTO>
     */
    public function active(): array;

    /**
     * Find a product by ID.
     */
    public function find(int $id): ?ProductDTO;

    /**
     * Search products by name or SKU.
     *
     * @return array<ProductDTO>
     */
    public function search(string $query): array;

    /**
     * Create a new product.
     */
    public function create(array $data): Product;

    /**
     * Update a product.
     */
    public function update(Product $product, array $data): bool;

    /**
     * Delete a product.
     */
    public function delete(Product $product): bool;

    /**
     * Find a product model by ID (internal use only).
     */
    public function findModel(int $id): ?Product;

    /**
     * Get all product models (internal use only).
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Product>
     */
    public function allModels(): \Illuminate\Database\Eloquent\Collection;
}
