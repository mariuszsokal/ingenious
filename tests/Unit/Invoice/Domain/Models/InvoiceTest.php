<?php

declare(strict_types=1);

namespace Tests\Unit\Invoice\Domain\Models;

use Modules\Invoices\Domain\Models\Invoice;
use Modules\Invoices\Domain\Models\InvoiceProductLine;
use Modules\Invoices\Domain\ValueObjects\InvoiceStatus;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class InvoiceTest extends TestCase
{
    public function testInvoiceCreatedWithDraftStatus(): void
    {
        $invoice = new Invoice('test', 'test');

        $this->assertEquals('draft', $invoice->getStatus()->getValue());
    }

    /**
     * @throws Exception
     */
    public function testAddInvoiceProductLineToInvoice(): void
    {
        $invoice = new Invoice('test', 'test');

        $this->assertCount(0, $invoice->getProductLines());

        $productLine = $this->createMock(InvoiceProductLine::class);
        $invoice->addProductLine($productLine);

        $this->assertCount(1, $invoice->getProductLines());
    }

    /**
     * @throws Exception
     */
    public function testInvoiceCalculatesTotalPrice(): void
    {
        $invoice = new Invoice('test', 'test');

        $productLine1 = $this->createConfiguredMock(
            InvoiceProductLine::class,
            ['getTotalUnitPrice' => 100]
        );
        $productLine2 = $this->createConfiguredMock(
            InvoiceProductLine::class,
            ['getTotalUnitPrice' => 100]
        );

        $invoice->addProductLine($productLine1);
        $invoice->addProductLine($productLine2);

        $this->assertCount(2, $invoice->getProductLines());
        $this->assertEquals(200, $invoice->getTotalPrice());
    }

    public function testEmptyInvoiceCalculatesTotalPrice(): void
    {
        $invoice = new Invoice('test', 'test');

        $this->assertEquals(0, $invoice->getTotalPrice());
    }

    /**
     * @throws Exception
     */
    public function testChangeStatus(): void
    {
        $invoice = new Invoice('test', 'test');

        $newStatus = $this->createMock(InvoiceStatus::class);
        $newStatus->method('getValue')->willReturn('sending');

        $invoice->changeStatus($newStatus);

        $this->assertEquals('sending', $invoice->getStatus()->getValue());
    }

}
