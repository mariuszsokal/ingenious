<?php

declare(strict_types=1);

namespace Modules\Invoices\Domain\Models;

use Modules\Invoices\Domain\ValueObjects\InvoiceStatus;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Invoice
{
    private UuidInterface $id;
    private InvoiceStatus $status;
    private string $customerName;
    private string $customerEmail;
    private array $productLines = [];
    private int $totalPrice = 0;

    public function __construct(
        string $customerName,
        string $customerEmail,
    ) {
        $this->id = Uuid::uuid4();
        $this->status = InvoiceStatus::draft();
        $this->customerName = $customerName;
        $this->customerEmail = $customerEmail;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getStatus(): InvoiceStatus
    {
        return $this->status;
    }

    public function changeStatus(InvoiceStatus $status): void
    {
        $this->status = $status;
    }

    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    public function getProductLines(): array
    {
        return $this->productLines;
    }

    public function addProductLine(InvoiceProductLine $productLine): void
    {
        $this->productLines[] = $productLine;
        $this->recalculateTotalPrice();
    }

    public function getTotalPrice(): int
    {
        return $this->totalPrice;
    }

    private function recalculateTotalPrice(): void
    {
        $this->totalPrice = array_reduce(
            $this->productLines,
            fn($carry, $line) => $carry + $line->getTotalUnitPrice(),
            0
        );
    }
}
