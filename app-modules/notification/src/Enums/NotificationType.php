<?php

declare(strict_types=1);

namespace AppModules\Notification\src\Enums;

enum NotificationType: string
{
    case INVOICE_CREATED = 'invoice_created';
    case PAYMENT_RECEIVED = 'payment_received';
    case INVOICE_OVERDUE = 'invoice_overdue';

    /**
     * Get the label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::INVOICE_CREATED => 'Invoice Created',
            self::PAYMENT_RECEIVED => 'Payment Received',
            self::INVOICE_OVERDUE => 'Invoice Overdue',
        };
    }
}
