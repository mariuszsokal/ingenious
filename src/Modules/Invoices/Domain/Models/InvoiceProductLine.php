<?php

declare(strict_types=1);

namespace Modules\Invoices\Domain\Models;

use Modules\Invoices\Domain\ValueObjects\Quantity;
use Modules\Invoices\Domain\ValueObjects\UnitPrice;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

readonly class InvoiceProductLine
{
    private UuidInterface $id;
    private UuidInterface $invoiceId;
    private string $productName;
    private Quantity $quantity;
    private UnitPrice $unitPrice;
    private int $totalUnitPrice;

    /**
     * In future usages like update Quantity or UnitPrice of InvoiceProductLine the totalUnitPrice
     * should be calculated somewhere else than in the constructor.
     */
    public function __construct(
        UuidInterface $invoiceId,
        string $productName,
        Quantity $quantity,
        UnitPrice $unitPrice,
    ) {
        $this->id = Uuid::uuid4();
        $this->invoiceId = $invoiceId;
        $this->productName = $productName;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
        $this->totalUnitPrice = $quantity->getValue() * $unitPrice->getValue();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getInvoiceId(): UuidInterface
    {
        return $this->invoiceId;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getQuantity(): Quantity
    {
        return $this->quantity;
    }

    public function getUnitPrice(): UnitPrice
    {
        return $this->unitPrice;
    }

    public function getTotalUnitPrice(): int
    {
        return $this->totalUnitPrice;
    }
}
