<?php

declare(strict_types=1);

namespace Modules\Invoices\Application\Listeners;

use Modules\Invoices\Application\Services\InvoiceService;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Invoices\Domain\ValueObjects\InvoiceStatus;
use Modules\Notifications\Api\Events\ResourceDeliveredEvent;

readonly class UpdateInvoiceStatusListener
{
    public function __construct(
        private InvoiceService $invoiceService,
        private InvoiceRepositoryInterface $invoiceRepository,
    ) {}

    /**
     * @throws \Exception
     */
    public function handle(ResourceDeliveredEvent $event): void
    {
        $invoice = $this->invoiceService->findInvoiceById($event->getResourceId());
        if (!$invoice) {
            throw new \Exception('Invoice not found.');
        }

        if (!$invoice->getStatus()->isSending()) {
            throw new \Exception('Invoice must be in sending status to mark as sent-to-client.');
        }

        $invoice->changeStatus(InvoiceStatus::sentToClient());
        $this->invoiceRepository->save($invoice);
    }
}
