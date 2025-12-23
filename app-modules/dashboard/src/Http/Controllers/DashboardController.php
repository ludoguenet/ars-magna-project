<?php

namespace AppModules\Dashboard\src\Http\Controllers;

use AppModules\Client\src\Repositories\ClientRepository;
use AppModules\Invoice\src\Enums\InvoiceStatus;
use AppModules\Invoice\src\Models\Invoice;
use AppModules\Product\src\Repositories\ProductRepository;
use Illuminate\View\View;

class DashboardController
{
    public function __construct(
        private ClientRepository $clientRepository,
        private ProductRepository $productRepository
    ) {}

    /**
     * Display the dashboard.
     */
    public function index(): View
    {
        $stats = [
            'total_clients' => $this->clientRepository->all()->count(),
            'total_products' => $this->productRepository->all()->count(),
            'total_invoices' => Invoice::count(),
            'total_revenue' => Invoice::where('status', InvoiceStatus::PAID)->sum('total'),
            'pending_invoices' => Invoice::where('status', InvoiceStatus::SENT)->count(),
            'overdue_invoices' => Invoice::overdue()->count(),
        ];

        $recentInvoices = Invoice::with(['client'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard::index', compact('stats', 'recentInvoices'));
    }
}
