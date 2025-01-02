<?php

declare(strict_types=1);

namespace Modules\Notifications\Presentation\Http;

use Illuminate\Http\JsonResponse;
use Modules\Notifications\Application\Services\NotificationService;
use Symfony\Component\HttpFoundation\Response;

final readonly class NotificationController
{
    public function __construct(
        private NotificationService $notificationService,
    ) {}

    public function hook(string $action, string $reference): JsonResponse
    {
        try {
            match ($action) {
                'delivered' => $this->notificationService->delivered(reference: $reference),
                default => null,
            };
        } catch (\Exception $exception) {
            return new JsonResponse(data: ['message' => $exception->getMessage()], status: Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(data: null, status: Response::HTTP_OK);
    }
}
