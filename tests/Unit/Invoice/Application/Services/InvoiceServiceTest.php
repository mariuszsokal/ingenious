<?php

declare(strict_types=1);

namespace Tests\Unit\Invoice\Application\Services;

use Modules\Invoices\Application\Services\InvoiceService;
use Modules\Invoices\Domain\Models\Invoice;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Invoices\Domain\ValueObjects\InvoiceStatus;
use Modules\Notifications\Application\Facades\NotificationFacade;
use Modules\Notifications\Infrastructure\Drivers\DriverInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class InvoiceServiceTest extends TestCase
{
    private InvoiceService $invoiceService;
    private InvoiceRepositoryInterface&MockObject $invoiceRepository;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->invoiceRepository = $this->createMock(InvoiceRepositoryInterface::class);
        $notificationFacade = new NotificationFacade(
            driver: $this->createMock(DriverInterface::class),
        );
        $this->invoiceService = new InvoiceService($notificationFacade, $this->invoiceRepository);
    }

    /**
     * @throws Exception
     */
    public function testCreateInvoice(): void
    {
        $customerName = 'test';
        $customerEmail = 'test';

        $this->invoiceRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($savedInvoice) use ($customerName, $customerEmail) {
                return $savedInvoice instanceof Invoice &&
                    $savedInvoice->getCustomerName() === $customerName &&
                    $savedInvoice->getCustomerEmail() === $customerEmail;
            }));

        $result = $this->invoiceService->createInvoice($customerName, $customerEmail);

        $this->assertInstanceOf(Invoice::class, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testSendInvoiceSuccessfully(): void
    {
        $uuid = Uuid::uuid4();
        $invoice = $this->createMock(Invoice::class);

        $invoice->method('getStatus')->willReturn(InvoiceStatus::draft());
        $invoice->method('getProductLines')->willReturn(['InvoiceProductLine']);
        $invoice->method('getId')->willReturn($uuid);
        $invoice->method('getCustomerEmail')->willReturn('test');

        $invoice->expects($this->once())->method('changeStatus')->with($this->callback(function ($status) {
            return $status->getValue() === 'sending';
        }));

        $this->invoiceRepository
            ->method('findById')
            ->with($uuid->toString())
            ->willReturn($invoice);

        $this->invoiceRepository
            ->expects($this->once())
            ->method('save')
            ->with($invoice);

        $this->invoiceService->sendInvoice($uuid);
    }


    public function testSendInvoiceNotFound(): void
    {
        $uuid = Uuid::uuid4();

        $this->invoiceRepository
            ->method('findById')
            ->with($uuid->toString())
            ->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invoice not found.');

        $this->invoiceService->sendInvoice($uuid);
    }

    /**
     * @throws Exception
     */
    public function testSendInvoiceNotInDraftStatus(): void
    {
        $uuid = Uuid::uuid4();
        $invoice = $this->createMock(Invoice::class);

        $invoice->method('getStatus')->willReturn(InvoiceStatus::sending());

        $this->invoiceRepository
            ->method('findById')
            ->with($uuid->toString())
            ->willReturn($invoice);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Only draft invoices can be sent.');

        $this->invoiceService->sendInvoice($uuid);
    }

    /**
     * @throws Exception
     */
    public function testSendInvoiceWithoutProductLines(): void
    {
        $uuid = Uuid::uuid4();
        $invoice = $this->createMock(Invoice::class);

        $invoice->method('getStatus')->willReturn(InvoiceStatus::draft());
        $invoice->method('getProductLines')->willReturn([]);

        $this->invoiceRepository
            ->method('findById')
            ->with($uuid->toString())
            ->willReturn($invoice);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('An invoice must contain at least one product line to be sent.');

        $this->invoiceService->sendInvoice($uuid);
    }

    /**
     * @throws Exception
     */
    public function testFindInvoiceByIdWithUuidInterface(): void
    {
        $uuid = Uuid::uuid4();
        $invoice = $this->createMock(Invoice::class);

        $this->invoiceRepository
            ->method('findById')
            ->with($uuid->toString())
            ->willReturn($invoice);

        $result = $this->invoiceService->findInvoiceById($uuid);

        $this->assertSame($invoice, $result);
    }

    /**
     * @throws Exception
     */
    public function testFindInvoiceByIdWithValidUuidString(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $invoice = $this->createMock(Invoice::class);

        $this->invoiceRepository
            ->method('findById')
            ->with($uuid)
            ->willReturn($invoice);

        $result = $this->invoiceService->findInvoiceById($uuid);

        $this->assertSame($invoice, $result);
    }

    public function testFindInvoiceByIdWithInvalidUuidString(): void
    {
        $invalidUuid = 'invalid-uuid-string';

        $this->invoiceRepository
            ->expects($this->never())
            ->method('findById');

        $result = $this->invoiceService->findInvoiceById($invalidUuid);

        $this->assertNull($result);
    }

    public function testFindInvoiceByIdWithNonUuidString(): void
    {
        $nonUuidString = 'random-text-string';

        $this->invoiceRepository
            ->expects($this->never())
            ->method('findById');

        $result = $this->invoiceService->findInvoiceById($nonUuidString);

        $this->assertNull($result);
    }
}
