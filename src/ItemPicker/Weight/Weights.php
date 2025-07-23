<?php

declare(strict_types=1);

namespace BenTools\Picker\ItemPicker\Weight;

use BenTools\Picker\Misc\WeakMap;
use Generator;
use WeakMap as NativeWeakMap;

final class Weights implements WeightProviderInterface
{
    /**
     * @var WeakMap<mixed, int>
     */
    private WeakMap $weights;

    private bool $hasWeightedItems = false;

    public function __construct()
    {
        $this->weights = new WeakMap();
    }

    public function getWeight(mixed $item): ?int
    {
        return $this->weights[$item] ?? null;
    }

    public function setWeight(mixed $item, int $weight): void
    {
        $this->hasWeightedItems = true;
        $this->weights[$item] = $weight;
    }

    public function hasWeightedItems(): bool
    {
        return $this->hasWeightedItems;
    }

    /**
     * @param Generator<mixed, int> $itemsWeightMap
     */
    public static function fromGenerator(Generator $itemsWeightMap): self
    {
        $weights = new self();
        foreach ($itemsWeightMap as $item => $weight) {
            $weights->setWeight($item, $weight);
        }

        return $weights;
    }

    /**
     * @param array<array-key, int> $itemsWeightMap
     */
    public static function fromAssociativeArray(array $itemsWeightMap): self
    {
        $weights = new self();
        foreach ($itemsWeightMap as $item => $weight) {
            $weights->setWeight($item, (int) $weight);
        }

        return $weights;
    }
    /**
     * @param WeakMap<mixed, int>|NativeWeakMap<object, int> $itemsWeightMap
     */
    public static function fromWeakMap(WeakMap|NativeWeakMap $itemsWeightMap): self
    {
        $weights = new self();
        foreach ($itemsWeightMap as $item => $weight) {
            $weights->setWeight($item, (int) $weight);
        }

        return $weights;
    }
}
