<?php

declare(strict_types=1);

namespace BenTools\Picker\ItemPicker\Algorithm;

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

    public function pick(PickerItemCollection $items, ItemPickerOptions $options): mixed
    {
        $this->currentIndex[$items] ??= -1;
        $maxIndex = count($items) - 1;

        $this->currentIndex[$items]++;
        if ($this->currentIndex[$items] > $maxIndex) {
            $this->currentIndex[$items] = 0; // Reset to the beginning
        }

        $index = $this->currentIndex[$items];

        return $items[$index];
    }
}
