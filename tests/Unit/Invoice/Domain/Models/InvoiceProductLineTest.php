<?php

declare(strict_types=1);

namespace Tests\Unit\Invoice\Domain\Models;

use Modules\Invoices\Domain\Exceptions\DomainException;
use Modules\Invoices\Domain\Models\InvoiceProductLine;
use Modules\Invoices\Domain\ValueObjects\Quantity;
use Modules\Invoices\Domain\ValueObjects\UnitPrice;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class InvoiceProductLineTest extends TestCase
{
    /**
     * @throws DomainException
     */
    public function testCalculateTotalUnitPrice(): void
    {
        $productLine = new InvoiceProductLine(
            Uuid::uuid4(),
            'InvoiceProductLine',
            new Quantity(25),
            new UnitPrice(2)
        );

        $this->assertEquals(50, $productLine->getTotalUnitPrice());
    }

    /**
     * @throws DomainException
     */
    public function testInvalidQuantityThrowsException(): void
    {
        $this->expectException(DomainException::class);

        new InvoiceProductLine(
            Uuid::uuid4(),
            'InvoiceProductLine',
            new Quantity(-1),
            new UnitPrice(1),
        );
    }

    public function testInvalidUnitPriceThrowsException(): void
    {
        $this->expectException(DomainException::class);

        new InvoiceProductLine(Uuid::uuid4(),
            'InvoiceProductLine',
            new Quantity(1),
            new UnitPrice(-1),
        );
    }
}
