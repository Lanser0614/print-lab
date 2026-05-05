<?php

namespace App\Support;

final class PrintAreaCalculator
{
    public static function fromRatio(
        int $canvasWidth,
        int $canvasHeight,
        float $x,
        float $y,
        float $width,
        float $height,
    ): PrintArea {
        return new PrintArea(
            x: (int) round($canvasWidth * $x),
            y: (int) round($canvasHeight * $y),
            width: (int) round($canvasWidth * $width),
            height: (int) round($canvasHeight * $height),
        );
    }
}
