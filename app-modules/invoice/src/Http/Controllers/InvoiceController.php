<?php

namespace AppModules\Invoice\src\Http\Controllers;

use AppModules\Invoice\src\DataTransferObjects\InvoiceData;
use AppModules\Invoice\src\Http\Requests\StoreInvoiceRequest;
use AppModules\Invoice\src\Repositories\InvoiceRepository;
use AppModules\Invoice\src\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController
{
    public function __construct(
        private InvoiceService $invoiceService,
        private InvoiceRepository $repository
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
        /** @var view-string $view */
        $view = 'invoice::create';

        return view($view);
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
            ->with('success', 'Facture créée avec succès');
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

        /** @var view-string $view */
        $view = 'invoice::edit';

        return view($view, compact('invoice'));
    }

    /**
     * Update the specified invoice.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        // TODO: Implement update logic
        return redirect()->route('invoice::show', $id);
    }

    /**
     * Remove the specified invoice.
     */
    public function destroy(int $id): RedirectResponse
    {
        $invoice = $this->repository->find($id);

        if ($invoice) {
            $this->repository->delete($invoice);
        }

        return redirect()
            ->route('invoice::index')
            ->with('success', 'Facture supprimée avec succès');
    }
}
