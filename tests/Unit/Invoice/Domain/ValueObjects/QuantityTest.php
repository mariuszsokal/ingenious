<?php

declare(strict_types=1);

namespace Tests\Unit\Invoice\Domain\ValueObjects;

use Modules\Invoices\Domain\Exceptions\DomainException;
use Modules\Invoices\Domain\ValueObjects\Quantity;
use PHPUnit\Framework\TestCase;

class QuantityTest extends TestCase
{
    /**
     * @throws DomainException
     */
    public function testValidQuantity(): void
    {
        $quantity = new Quantity(1);
        $this->assertEquals(1, $quantity->getValue());
    }

    /**
     * @throws DomainException
     */
    public function testIsEquals(): void
    {
        $quantity1 = new Quantity(1);
        $quantity2 = new Quantity(1);

        $this->assertTrue($quantity1->equals($quantity2));
    }

    /**
     * @throws DomainException
     */
    public function testIsNotEquals(): void
    {
        $quantity1 = new Quantity(1);
        $quantity2 = new Quantity(2);

        $this->assertFalse($quantity1->equals($quantity2));
    }

    /**
     * @dataProvider invalidQuantityProvider
     */
    public function testInvalidQuantity(int $quantity): void
    {
        $this->expectException(DomainException::class);

        new Quantity($quantity);
    }

    public static function invalidQuantityProvider(): array
    {
        return [
            'zero' => [0],
            'negative one' => [-1],
        ];
    }
}
