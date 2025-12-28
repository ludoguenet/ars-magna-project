<?php

declare(strict_types=1);

namespace AppModules\Notification\src\DataTransferObjects;

use AppModules\Notification\src\Enums\NotificationType;
use AppModules\Notification\src\Models\Notification;

final readonly class NotificationDTO
{
    public function __construct(
        public ?int $id = null,
        public int $userId = 0,
        public ?NotificationType $type = null,
        public string $title = '',
        public string $message = '',
        public array $data = [],
        public ?\DateTime $readAt = null,
        public ?\DateTime $createdAt = null,
        public ?\DateTime $updatedAt = null,
    ) {}

    /**
     * Create from Eloquent model.
     */
    public static function fromModel(Notification $notification): self
    {
        return new self(
            id: $notification->id,
            userId: $notification->user_id,
            type: NotificationType::from($notification->type),
            title: $notification->title,
            message: $notification->message,
            data: $notification->data ?? [],
            readAt: $notification->read_at ? \DateTime::createFromInterface($notification->read_at) : null,
            createdAt: $notification->created_at ? \DateTime::createFromInterface($notification->created_at) : null,
            updatedAt: $notification->updated_at ? \DateTime::createFromInterface($notification->updated_at) : null,
        );
    }
}
