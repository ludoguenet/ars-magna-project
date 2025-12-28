<?php

declare(strict_types=1);

namespace AppModules\Notification\src\Actions;

use AppModules\Notification\src\Contracts\NotificationRepositoryContract;
use AppModules\Notification\src\DataTransferObjects\NotificationDTO;
use AppModules\Notification\src\Models\Notification;

class CreateNotificationAction
{
    public function __construct(
        private NotificationRepositoryContract $repository
    ) {}

    /**
     * Execute the action.
     */
    public function handle(NotificationDTO $data): Notification
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
