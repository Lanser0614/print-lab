<?php

namespace Tests\Unit;

use App\Support\PrintAreaCalculator;
use PHPUnit\Framework\TestCase;

class PrintAreaCalculatorTest extends TestCase
{
    public function test_it_calculates_print_area_pixels_from_ratio_values(): void
    {
        $area = PrintAreaCalculator::fromRatio(
            canvasWidth: 600,
            canvasHeight: 600,
            x: 0.32,
            y: 0.27,
            width: 0.36,
            height: 0.42,
        );

        $this->assertSame(192, $area->x);
        $this->assertSame(162, $area->y);
        $this->assertSame(216, $area->width);
        $this->assertSame(252, $area->height);
    }
}
