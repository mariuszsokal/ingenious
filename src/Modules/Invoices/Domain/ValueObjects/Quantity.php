<?php

declare(strict_types=1);

namespace Modules\Invoices\Domain\ValueObjects;

use Modules\Invoices\Domain\Exceptions\DomainException;

final readonly class Quantity
{
    private int $value;

    /**
     * @throws DomainException
     */
    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new DomainException('Quantity must be a positive integer.');
        }

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function equals(Quantity $quantity): bool
    {
        return $this->value === $quantity->getValue();
    }
}
