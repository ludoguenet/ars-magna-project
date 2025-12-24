<?php

namespace AppModules\Product\src\Http\Controllers;

use AppModules\Product\src\Http\Requests\StoreProductRequest;
use AppModules\Product\src\Http\Requests\UpdateProductRequest;
use AppModules\Product\src\Repositories\ProductRepository;
use AppModules\Product\src\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController
{
    public function __construct(
        private ProductService $productService,
        private ProductRepository $repository
    ) {}

    /**
     * Display a listing of products.
     */
    public function index(Request $request): View
    {
        // Internal use - can use models directly
        $products = $this->repository->allModels();

        /** @var view-string $view */
        $view = 'product::index';

        return view($view, compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        /** @var view-string $view */
        $view = 'product::create';

        return view($view);
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = $this->productService->create($request->validated());

        return redirect()
            ->route('product::show', $product)
            ->with('success', 'Product created successfully');
    }

    /**
     * Display the specified product.
     */
    public function show(int $id): View
    {
        // Internal use - can use models directly
        $product = $this->repository->findModel($id);

        if (! $product) {
            abort(404);
        }

        /** @var view-string $view */
        $view = 'product::show';

        return view($view, compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(int $id): View
    {
        // Internal use - can use models directly
        $product = $this->repository->findModel($id);

        if (! $product) {
            abort(404);
        }

        /** @var view-string $view */
        $view = 'product::edit';

        return view($view, compact('product'));
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, int $id): RedirectResponse
    {
        // Internal use - can use models directly
        $product = $this->repository->findModel($id);

        if (! $product) {
            abort(404);
        }

        $this->productService->update($product, $request->validated());

        return redirect()
            ->route('product::show', $product)
            ->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(int $id): RedirectResponse
    {
        // Internal use - can use models directly
        $product = $this->repository->findModel($id);

        if ($product) {
            $this->productService->delete($product);
        }

        return redirect()
            ->route('product::index')
            ->with('success', 'Product deleted successfully');
    }
}
