<?php

declare(strict_types=1);

namespace Tests\Unit\Invoice\Domain\ValueObjects;

use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\Exceptions\DomainException;
use Modules\Invoices\Domain\ValueObjects\InvoiceStatus;
use PHPUnit\Framework\TestCase;

class InvoiceStatusTest extends TestCase
{
    public function testCanCreateDraftStatus(): void
    {
        $status = InvoiceStatus::draft();

        $this->assertEquals('draft', $status->getValue());
        $this->assertTrue($status->isDraft());
        $this->assertFalse($status->isSending());
        $this->assertFalse($status->isSentToClient());
    }

    public function testCanCreateSendingStatus(): void
    {
        $status = InvoiceStatus::sending();

        $this->assertEquals('sending', $status->getValue());
        $this->assertFalse($status->isDraft());
        $this->assertTrue($status->isSending());
        $this->assertFalse($status->isSentToClient());
    }

    public function testCanCreateSentToClientStatus(): void
    {
        $status = InvoiceStatus::sentToClient();

        $this->assertEquals('sent-to-client', $status->getValue());
        $this->assertFalse($status->isDraft());
        $this->assertFalse($status->isSending());
        $this->assertTrue($status->isSentToClient());
    }

    public function testCannotCreateInvalidStatus(): void
    {
        $this->expectException(DomainException::class);

        InvoiceStatus::fromString('invalid-status');
    }

    public function testValidStatusTransitions(): void
    {
        $status = InvoiceStatus::draft();

        $newStatus = $status->transitionTo(StatusEnum::Sending);
        $this->assertEquals('sending', $newStatus->getValue());
    }
}
