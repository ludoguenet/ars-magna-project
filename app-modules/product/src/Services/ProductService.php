<?php

declare(strict_types=1);

namespace AppModules\Product\src\Services;

use AppModules\Product\src\Contracts\ProductRepositoryContract;
use AppModules\Product\src\Models\Product;

class ProductService
{
    public function __construct(
        private ProductRepositoryContract $repository
    ) {}

    /**
     * Create a new product.
     */
    public function create(array $data): Product
    {
        return $this->repository->create($data);
    }

    /**
     * Update a product.
     */
    public function update(Product $product, array $data): Product
    {
        $this->repository->update($product, $data);

        return $product->fresh();
    }

    /**
     * Delete a product.
     */
    public function delete(Product $product): bool
    {
        return $this->repository->delete($product);
    }
}
