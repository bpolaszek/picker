<?php

declare(strict_types=1);

namespace BenTools\Picker\ItemPicker\Algorithm;

use BenTools\Picker\ItemPicker\ItemPicker;
use BenTools\Picker\ItemPicker\ItemPickerOptions;
use BenTools\Picker\ItemPicker\PickerItemCollection;
use WeakMap;

use function count;

/**
 * @internal
 */
final class RoundRobinAlgorithm implements PickerAlgorithmInterface
{
    private WeakMap $currentIndex;

    public function __construct()
    {
        $this->currentIndex = new WeakMap();
    }

    public function pick(PickerItemCollection $items, ItemPickerOptions $options, ItemPicker $picker): mixed
    {
        $this->currentIndex[$picker] ??= -1;
        $maxIndex = count($items) - 1;

        $this->currentIndex[$picker]++;
        if ($this->currentIndex[$picker] > $maxIndex) {
            $this->currentIndex[$picker] = 0; // Reset to the beginning
        }

        $index = $this->currentIndex[$picker];

        return $items[$index];
    }
}
