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

        NextIndex:
        $this->currentIndex[$items]->currentIndex++;
        if ($this->currentIndex[$items]->currentIndex > $maxIndex) {
            $this->currentIndex[$items] = 0; // Reset to the beginning
        }

        if (!isset($items[$state->currentIndex])) {
            goto NextIndex;
        }

        $index = $this->currentIndex[$items];

        return $items[$index];
    }
}
