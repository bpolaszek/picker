<?php

declare(strict_types=1);

namespace BenTools\Picker\ItemPicker\Algorithm;

use BenTools\Picker\ItemPicker\ItemPickerOptions;
use BenTools\Picker\ItemPicker\PickerItemCollection;
use BenTools\Picker\Misc\WeakMap;
use BenTools\Picker\NumberPicker\NumberPicker;
use InvalidArgumentException;
use WeakMap as NativeWeakMap;

/**
 * @internal
 */
final readonly class ProbabilisticAlgorithm implements PickerAlgorithmInterface
{
    /**
     * @var NativeWeakMap<PickerItemCollection, int>
     */
    private NativeWeakMap $seeds;

    public function __construct()
    {
        $this->seeds = new NativeWeakMap();
    }

    /**
     * @template T
     * @param PickerItemCollection<T> $items
     *
     * @return T
     */
    public function pick(PickerItemCollection $items, ItemPickerOptions $options): mixed
    {
        $totalWeight = 0;
        $cumulativeWeights = new WeakMap();
        $indexes = new  WeakMap();
        if (!isset($this->seeds[$items])) {
            $this->seeds->offsetSet($items, $options->seed);
        }

        foreach ($items as $index => $item) {
            $indexes[$item] = $index;

            $weight = $options->weights->getWeight($item) ?? $options->defaultWeight;

            if ($weight < 0) {
                throw new InvalidArgumentException('Weight must be non-negative');
            }

            $totalWeight += $weight;
            $cumulativeWeights[$item] = $totalWeight;
        }

        if ($totalWeight === 0) {
            throw new InvalidArgumentException('Total weight must be greater than 0');
        }

        $random = NumberPicker::randomInt(0, $totalWeight - 1, $this->seeds[$items]);

        if (null !== $options->seed) {
            $this->seeds->offsetSet($items, $this->seeds[$items] + 1);
        }

        foreach ($cumulativeWeights as $item => $cumulativeWeight) {
            if ($random < $cumulativeWeight) {
                return $items[$indexes[$item]];
            }
        }

        throw new \RuntimeException('Unable to pick an item, this should not happen'); // @codeCoverageIgnore
    }

}
