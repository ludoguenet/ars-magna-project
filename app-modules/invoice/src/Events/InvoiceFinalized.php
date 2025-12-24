<?php

namespace AppModules\Invoice\src\Events;

use AppModules\Invoice\src\DataTransferObjects\InvoiceDTO;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoiceFinalized
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public InvoiceDTO $invoice
    ) {}
}
