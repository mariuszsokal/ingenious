<?php

declare(strict_types=1);

namespace Modules\Invoices\Domain\ValueObjects;

use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\Exceptions\DomainException;

readonly class InvoiceStatus
{
    private function __construct(private StatusEnum $status) {}

    /**
     * @throws DomainException
     */
    public static function fromString(string $status): self
    {
        if (!StatusEnum::tryFrom($status)) {
            throw new DomainException("Invalid status value: $status");
        }

        return new self(StatusEnum::from($status));
    }

    public static function draft(): self
    {
        return new self(StatusEnum::Draft);
    }

    public static function sending(): self
    {
        return new self(StatusEnum::Sending);
    }

    public static function sentToClient(): self
    {
        return new self(StatusEnum::SentToClient);
    }

    public function getValue(): string
    {
        return $this->status->value;
    }

    public function isDraft(): bool
    {
        return $this->status === StatusEnum::Draft;
    }

    public function isSending(): bool
    {
        return $this->status === StatusEnum::Sending;
    }

    public function isSentToClient(): bool
    {
        return $this->status === StatusEnum::SentToClient;
    }

    public function transitionTo(StatusEnum $newStatus): self
    {
        return new self($newStatus);
    }
}
