<?php

declare(strict_types=1);

namespace Modules\Notifications\Infrastructure\Drivers;

use Modules\Notifications\Api\Events\ResourceDeliveredEvent;
use Ramsey\Uuid\Uuid;

class DummyDriver implements DriverInterface
{
    public function send(
        string $toEmail,
        string $subject,
        string $message,
        string $reference,
    ): bool {
        event(new ResourceDeliveredEvent(Uuid::fromString($reference)));
        return true;
    }
}
