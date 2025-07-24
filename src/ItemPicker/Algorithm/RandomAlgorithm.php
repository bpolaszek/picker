<?php

declare(strict_types=1);

namespace BenTools\Picker\ItemPicker\Algorithm;

use BenTools\Picker\ItemPicker\ItemPickerOptions;
use BenTools\Picker\ItemPicker\PickerItemCollection;
use BenTools\Picker\NumberPicker\NumberPicker;
use WeakMap as NativeWeakMap;

use function count;
use function max;

/**
 * @internal
 */
final readonly class RandomAlgorithm implements PickerAlgorithmInterface
{
    /**
     * @var NativeWeakMap<PickerItemCollection, int>
     */
    private NativeWeakMap $seeds;

    public function __construct()
    {
        $this->seeds = new NativeWeakMap();
    }

    public function pick(PickerItemCollection $items, ItemPickerOptions $options): mixed
    {
        if (!isset($this->seeds[$items])) {
            $this->seeds->offsetSet($items, $options->seed);
        }
        $maxIndex = count($items) - 1;
        $nextIndex = NumberPicker::randomInt(0, max(0, $maxIndex), $this->seeds[$items]);

        if (null !== $options->seed) {
            $this->seeds->offsetSet($items, $this->seeds[$items] + 1);
        }

        return $items[$nextIndex];
    }
}
