<?php

declare(strict_types=1);

namespace BenTools\Picker;

use BenTools\Picker\NumberPicker\NumberPicker;

function random_int(int $min, int $max, ?int $seed = null): int
{
    return NumberPicker::randomInt($min, $max, $seed);
}
