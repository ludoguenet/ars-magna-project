<?php

namespace AppModules\Invoice\src\Events;

use AppModules\Invoice\src\Models\Invoice;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoicePaid
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Invoice $invoice
    ) {}
}
