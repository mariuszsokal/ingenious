<?php

declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\Repositories;

use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Invoices\Domain\Models\Invoice as DomainInvoice;
use Modules\Invoices\Infrastructure\Models\Invoice as EloquentInvoice;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function findById(string $id): ?DomainInvoice
    {
        $eloquentInvoice = EloquentInvoice::with('productLines')->find($id);

        if (!$eloquentInvoice) {
            return null;
        }

        return new DomainInvoice(
            $eloquentInvoice->customer_name,
            $eloquentInvoice->customer_email
        );
    }

    public function save(DomainInvoice $invoice): void
    {
        $eloquentInvoice = EloquentInvoice::find($invoice->getId()) ?? new EloquentInvoice();
        $eloquentInvoice->id = $invoice->getId();
        $eloquentInvoice->customer_name = $invoice->getCustomerName();
        $eloquentInvoice->customer_email = $invoice->getCustomerEmail();
        $eloquentInvoice->status = $invoice->getStatus()->getValue();

        $eloquentInvoice->save();
    }
}
