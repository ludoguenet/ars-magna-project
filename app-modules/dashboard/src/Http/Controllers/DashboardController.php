<?php

namespace AppModules\Dashboard\src\Http\Controllers;

use AppModules\Client\src\Contracts\ClientRepositoryContract;
use AppModules\Invoice\src\Contracts\InvoiceRepositoryContract;
use AppModules\Product\src\Contracts\ProductRepositoryContract;
use Illuminate\View\View;

class DashboardController
{
    public function __construct(
        private ClientRepositoryContract $clientRepository,
        private ProductRepositoryContract $productRepository,
        private InvoiceRepositoryContract $invoiceRepository
    ) {}

    /**
     * Display the dashboard.
     */
    public function index(): View
    {
        $stats = [
            'total_clients' => count($this->clientRepository->all()),
            'total_products' => count($this->productRepository->all()),
            'total_invoices' => $this->invoiceRepository->count(),
            'total_revenue' => $this->invoiceRepository->getTotalRevenue(),
            'pending_invoices' => $this->invoiceRepository->getPendingCount(),
            'overdue_invoices' => $this->invoiceRepository->getOverdueCount(),
        ];

        $recentInvoices = $this->invoiceRepository->getRecentInvoices(5);

        return view('dashboard::index', compact('stats', 'recentInvoices'));
    }
}
