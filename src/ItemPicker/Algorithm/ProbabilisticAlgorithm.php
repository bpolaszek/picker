<?php

declare(strict_types=1);

namespace BenTools\Picker\ItemPicker\Algorithm;

use BenTools\Picker\ItemPicker\ItemPickerOptions;
use BenTools\Picker\ItemPicker\PickerItemCollection;
use BenTools\Picker\Misc\WeakMap;
use BenTools\Picker\NumberPicker\NumberPicker;
use InvalidArgumentException;

/**
 * @internal
 */
final readonly class ProbabilisticAlgorithm implements PickerAlgorithmInterface
{
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

        $random = NumberPicker::randomInt(0, $totalWeight - 1, $options->seed);

        if (null !== $options->seed) {
            $options->seed++;
        }

        foreach ($cumulativeWeights as $item => $cumulativeWeight) {
            if ($random < $cumulativeWeight) {
                return $items[$indexes[$item]];
            }
        }
        throw new \RuntimeException('Unable to pick an item, this should not happen');
    }

}
