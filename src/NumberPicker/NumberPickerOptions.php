<?php

declare(strict_types=1);

namespace BenTools\Picker\NumberPicker;

final readonly class NumberPickerOptions
{
    public function __construct(
        public ?int $seed = null,
    ) {
    }
}
