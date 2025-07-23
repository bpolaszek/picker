<?php

declare(strict_types=1);

namespace BenTools\Picker;

use BenTools\Picker\ItemPicker\Algorithm\Algorithm;
use BenTools\Picker\ItemPicker\ItemPicker;
use BenTools\Picker\ItemPicker\ItemPickerOptions;
use BenTools\Picker\NumberPicker\NumberPicker;
use BenTools\Picker\NumberPicker\NumberPickerOptions;

final readonly class Picker implements PickerInterface
{
    private function __construct(
        private PickerInterface $picker,
    ) {
    }

    public function pick(): mixed
    {
        return $this->picker->pick();
    }

    public function withAlgorithm(Algorithm $algorithm): PickerInterface
    {

    }

    /**
     * @template T
     * @param iterable<T> $items
     *
     * @return PickerInterface<T>
     */
    public static function fromItems(
        iterable $items,
        ItemPickerOptions $options = new ItemPickerOptions(),
    ): PickerInterface {
        return ItemPicker::create($items, $options);
    }

    public static function betweenNumbers(
        int $min,
        int $max,
        NumberPickerOptions $options = new NumberPickerOptions(),
    ): PickerInterface {
        return NumberPicker::create($min, $max, $options);
    }
}
