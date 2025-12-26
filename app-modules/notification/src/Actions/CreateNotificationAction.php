<?php

declare(strict_types=1);

namespace AppModules\Notification\src\Actions;

use AppModules\Notification\src\DataTransferObjects\NotificationData;
use AppModules\Notification\src\Models\Notification;
use AppModules\Notification\src\Repositories\NotificationRepository;

class CreateNotificationAction
{
    public function __construct(
        private NotificationRepository $repository
    ) {}

    /**
     * Execute the action.
     */
    public function handle(NotificationData $data): Notification
    {
        return $this->repository->create([
            'user_id' => $data->userId,
            'type' => $data->type->value,
            'title' => $data->title,
            'message' => $data->message,
            'data' => $data->data,
        ]);
    }
}
