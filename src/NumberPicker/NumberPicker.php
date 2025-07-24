<?php

declare(strict_types=1);

namespace BenTools\Picker\NumberPicker;

use BenTools\Picker\PickerInterface;
use ValueError;

use function abs;
use function random_int;

final class NumberPicker implements PickerInterface
{
    private const LCG_MULTIPLIER = 1664525;
    private const LCG_INCREMENT = 1013904223;
    private const LCG_MODULUS = 2 ** 32;
    public ?int $seed = null;

    private function __construct(
        public readonly int $min,
        public readonly int $max,
        public readonly NumberPickerOptions $options = new NumberPickerOptions(),
    ) {
        $this->seed = $options->seed;
    }

    public function pick(): int
    {
        $result = self::randomInt($this->min, $this->max, $this->seed);

        if ($this->options->incrementSeed) {
            $this->seed++;
        }

        return $result;
    }

    public static function randomInt(int $min, int $max, ?int $seed = null): int
    {
        if (null === $seed) {
            return random_int($min, $max);
        }

        if ($max < $min) {
            throw new ValueError(
                __METHOD__ . ': Argument #1 ($min) must be less than or equal to argument #2 ($max)',
            );
        }

        $seed = (self::LCG_MULTIPLIER * $seed + self::LCG_INCREMENT) % self::LCG_MODULUS;

        return $min + abs($seed) % ($max - $min + 1);
    }

    public static function create(
        int $min,
        int $max,
        NumberPickerOptions $options = new NumberPickerOptions(),
    ): self {
        return new self($min, $max, $options);
    }
}
