<?php

declare(strict_types=1);

namespace AppModules\Invoice\src\Events;

use AppModules\Invoice\src\DataTransferObjects\InvoiceDTO;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoicePaid
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public InvoiceDTO $invoice
    ) {}
}
