<?php

declare(strict_types=1);

namespace Modules\Invoices\Application\Services;

use Modules\Invoices\Domain\Models\Invoice;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Invoices\Domain\ValueObjects\InvoiceStatus;
use Modules\Notifications\Api\Dtos\NotifyData;
use Modules\Notifications\Application\Facades\NotificationFacade;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class InvoiceService
{
    public function __construct(
        private readonly NotificationFacade $notificationFacade,
        private readonly InvoiceRepositoryInterface $invoiceRepository
    ) {}

    public function findInvoiceById(string|UuidInterface $id): ?Invoice
    {
        if (is_string($id)) {
            try {
                $id = Uuid::fromString($id);
            } catch (\Exception $unused) {
                return null;
            }
        }

        return $this->invoiceRepository->findById($id->toString());
    }


    public function createInvoice(
        string $customerName,
        string $customerEmail,
    ): Invoice {
        $invoice = new Invoice($customerName, $customerEmail);

        $this->invoiceRepository->save($invoice);

        return $invoice;
    }

    /**
     * @throws \Exception
     */
    public function sendInvoice(UuidInterface $id): void
    {
        $invoice = $this->findInvoiceById($id);

        if (!$invoice) {
            throw new \Exception('Invoice not found.');
        }

        if (!$invoice->getStatus()->isDraft()) {
            throw new \Exception('Only draft invoices can be sent.');
        }

        if (empty($invoice->getProductLines())) {
            throw new \Exception('An invoice must contain at least one product line to be sent.');
        }

        $this->notificationFacade->notify(new NotifyData(
            $invoice->getId(),
            $invoice->getCustomerEmail(),
            'subject',
            'message',
        ));

        $invoice->changeStatus(InvoiceStatus::sending());
        $this->invoiceRepository->save($invoice);
    }
}
