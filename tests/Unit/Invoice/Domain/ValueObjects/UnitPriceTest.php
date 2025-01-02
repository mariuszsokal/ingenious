<?php

declare(strict_types=1);

namespace Tests\Unit\Invoice\Domain\ValueObjects;

use Modules\Invoices\Domain\Exceptions\DomainException;
use Modules\Invoices\Domain\ValueObjects\UnitPrice;
use PHPUnit\Framework\TestCase;

class UnitPriceTest extends TestCase
{
    /**
     * @throws DomainException
     */
    public function validUnitPrice(): void
    {
        $unitPrice = new UnitPrice(1);
        $this->assertEquals(1, $unitPrice->getValue());
    }

    /**
     * @throws DomainException
     */
    public function testIsEquals(): void
    {
        $unitPrice1 = new UnitPrice(1);
        $unitPrice2 = new UnitPrice(1);

        $this->assertTrue($unitPrice1->equals($unitPrice2));
    }

    /**
     * @throws DomainException
     */
    public function testIsNotEquals(): void
    {
        $unitPrice1 = new UnitPrice(1);
        $unitPrice2 = new UnitPrice(2);

        $this->assertFalse($unitPrice1->equals($unitPrice2));
    }

    /**
     * @dataProvider invalidUnitPriceProvider
     */
    public function testInvalidUnitPrice(int $unitPrice): void
    {
        $this->expectException(DomainException::class);

        new UnitPrice($unitPrice);
    }

    public static function invalidUnitPriceProvider(): array
    {
        return [
            'zero' => [0],
            'negative one' => [-1],
        ];
    }
}
