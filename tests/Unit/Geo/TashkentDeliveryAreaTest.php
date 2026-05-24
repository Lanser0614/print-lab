<?php

namespace Tests\Unit\Geo;

use PHPUnit\Framework\TestCase;
use App\Support\Geo\TashkentDeliveryArea;

class TashkentDeliveryAreaTest extends TestCase
{
    public function test_it_accepts_coordinates_inside_tashkent(): void
    {
        $this->assertTrue(TashkentDeliveryArea::contains(41.2995, 69.2401));
    }

    public function test_it_rejects_coordinates_outside_tashkent(): void
    {
        $this->assertFalse(TashkentDeliveryArea::contains(39.6542, 66.9597));
    }

    public function test_it_accepts_supported_city_names(): void
    {
        $this->assertSame('Tashkent', TashkentDeliveryArea::normalizeCity('Tashkent'));
        $this->assertSame('Tashkent', TashkentDeliveryArea::normalizeCity('Toshkent'));
        $this->assertSame('Tashkent', TashkentDeliveryArea::normalizeCity('Ташкент'));
    }

    public function test_it_rejects_unsupported_city_names(): void
    {
        $this->assertNull(TashkentDeliveryArea::normalizeCity('Samarkand'));
    }
}
