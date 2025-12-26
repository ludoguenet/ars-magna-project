<?php

declare(strict_types=1);

namespace AppModules\Notification\src\Listeners;

use AppModules\Invoice\src\Events\InvoiceCreated;
use AppModules\Notification\src\Actions\CreateNotificationAction;
use AppModules\Notification\src\DataTransferObjects\NotificationData;
use AppModules\Notification\src\Enums\NotificationType;

class HandleInvoiceCreated
{
    public function __construct(
        private CreateNotificationAction $createNotificationAction
    ) {}

    /**
     * Handle the InvoiceCreated event.
     */
    public function handle(InvoiceCreated $event): void
    {
        $invoice = $event->invoice;

        // Notifications go to the authenticated user managing the system
        // If no user is authenticated (e.g., in queue jobs), skip notification
        if (! auth()->check()) {
            return;
        }

        $this->createNotificationAction->handle(
            new NotificationData(
                userId: auth()->id(),
                type: NotificationType::INVOICE_CREATED,
                title: 'New Invoice Created',
                message: "Invoice #{$invoice->invoiceNumber} has been created. Total: ".number_format($invoice->total, 2).'.',
                data: [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoiceNumber,
                    'total' => $invoice->total,
                ]
            )
        );
    }
}
