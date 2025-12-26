<?php

declare(strict_types=1);

namespace AppModules\Notification\src\Listeners;

use AppModules\Invoice\src\Events\InvoicePaid;
use AppModules\Notification\src\Actions\CreateNotificationAction;
use AppModules\Notification\src\DataTransferObjects\NotificationData;
use AppModules\Notification\src\Enums\NotificationType;

class HandleInvoicePaid
{
    public function __construct(
        private CreateNotificationAction $createNotificationAction
    ) {}

    /**
     * Handle the InvoicePaid event.
     */
    public function handle(InvoicePaid $event): void
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
                type: NotificationType::PAYMENT_RECEIVED,
                title: 'Invoice Paid',
                message: "Invoice #{$invoice->invoiceNumber} has been paid. Amount: ".number_format($invoice->total, 2).'.',
                data: [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoiceNumber,
                    'total' => $invoice->total,
                ]
            )
        );
    }
}
