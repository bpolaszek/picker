<?php

declare(strict_types=1);

namespace BenTools\Picker\ItemPicker\Algorithm;

use BenTools\Picker\ItemPicker\ItemPicker;
use BenTools\Picker\ItemPicker\ItemPickerOptions;
use BenTools\Picker\ItemPicker\PickerItemCollection;
use BenTools\Picker\NumberPicker\NumberPicker;

use function count;
use function max;

/**
 * @internal
 */
final readonly class RandomAlgorithm implements PickerAlgorithmInterface
{
    public function pick(PickerItemCollection $items, ItemPickerOptions $options, ItemPicker $picker): mixed
    {
        $maxIndex = count($items) - 1;
        $nextIndex = NumberPicker::randomInt(0, max(0, $maxIndex), $picker->getSeed());
        $picker->updateSeed();

        return $items[$nextIndex];
    }
}
