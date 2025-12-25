<?php

declare(strict_types=1);

namespace AppModules\Invoice\src\Http\Controllers;

use AppModules\Client\src\Contracts\ClientRepositoryContract;
use AppModules\Invoice\src\Actions\MarkInvoiceAsPaidAction;
use AppModules\Invoice\src\Contracts\InvoiceRepositoryContract;
use AppModules\Invoice\src\DataTransferObjects\InvoiceData;
use AppModules\Invoice\src\Http\Requests\MarkInvoiceAsPaidRequest;
use AppModules\Invoice\src\Http\Requests\StoreInvoiceRequest;
use AppModules\Invoice\src\Services\InvoiceService;
use AppModules\Product\src\Contracts\ProductRepositoryContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController
{
    public function __construct(
        private InvoiceService $invoiceService,
        private InvoiceRepositoryContract $repository,
        private ClientRepositoryContract $clientRepository,
        private ProductRepositoryContract $productRepository,
        private MarkInvoiceAsPaidAction $markAsPaidAction
    ) {}

    /**
     * Display a listing of invoices.
     */
    public function index(Request $request): View
    {
        $invoices = $this->repository->all();

        /** @var view-string $view */
        $view = 'invoice::index';

        return view($view, compact('invoices'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create(): View
    {
        $clients = $this->clientRepository->all();
        $products = $this->productRepository->active();

        /** @var view-string $view */
        $view = 'invoice::create';

        return view($view, compact('clients', 'products'));
    }

    /**
     * Store a newly created invoice.
     */
    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $invoice = $this->invoiceService->createCompleteInvoice(
            InvoiceData::fromRequest($request),
            $request->input('items', [])
        );

        return redirect()
            ->route('invoice::show', $invoice)
            ->with('success', 'Invoice created successfully');
    }

    /**
     * Display the specified invoice.
     */
    public function show(int $id): View
    {
        $invoice = $this->repository->find($id);

        if (! $invoice) {
            abort(404);
        }

        /** @var view-string $view */
        $view = 'invoice::show';

        return view($view, compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(int $id): View
    {
        $invoice = $this->repository->find($id);

        if (! $invoice) {
            abort(404);
        }

        $clients = $this->clientRepository->all();
        $products = $this->productRepository->active();

        /** @var view-string $view */
        $view = 'invoice::edit';

        return view($view, compact('invoice', 'clients', 'products'));
    }

    /**
     * Update the specified invoice.
     */
    public function update(StoreInvoiceRequest $request, int $id): RedirectResponse
    {
        $invoice = $this->repository->findModel($id);

        if (! $invoice) {
            abort(404);
        }

        $invoice = $this->invoiceService->updateCompleteInvoice(
            $invoice,
            InvoiceData::fromRequest($request),
            $request->input('items', [])
        );

        return redirect()
            ->route('invoice::show', $invoice)
            ->with('success', 'Invoice updated successfully');
    }

    /**
     * Finalize the specified invoice.
     */
    public function finalize(int $id): RedirectResponse
    {
        $invoice = $this->repository->findModel($id);

        if (! $invoice) {
            abort(404);
        }

        try {
            $this->invoiceService->finalizeInvoice($invoice);

            return redirect()
                ->route('invoice::show', $id)
                ->with('success', 'Invoice finalized and sent successfully');
        } catch (\DomainException $e) {
            return redirect()
                ->route('invoice::show', $id)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Mark the specified invoice as paid.
     */
    public function markAsPaid(MarkInvoiceAsPaidRequest $request, int $id): RedirectResponse
    {
        $invoice = $this->repository->findModel($id);

        if (! $invoice) {
            abort(404);
        }

        try {
            $this->markAsPaidAction->handle($invoice);

            return redirect()
                ->route('invoice::show', $id)
                ->with('success', 'Invoice marked as paid successfully');
        } catch (\DomainException $e) {
            return redirect()
                ->route('invoice::show', $id)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified invoice.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->repository->deleteById($id);

        return redirect()
            ->route('invoice::index')
            ->with('success', 'Invoice deleted successfully');
    }
}
