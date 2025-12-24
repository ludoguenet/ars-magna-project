<?php

namespace AppModules\Product\src\Contracts;

use AppModules\Product\src\DataTransferObjects\ProductDTO;

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
}
