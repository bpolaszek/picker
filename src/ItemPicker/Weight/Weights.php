<?php

declare(strict_types=1);

namespace BenTools\Picker\ItemPicker\Weight;

use BenTools\Picker\Misc\WeakMap;
use Generator;

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
     * @return self
     */
    public static function fromGenerator(Generator $itemsWeightMap): self
    {
        $weights = new self();
        foreach ($itemsWeightMap as $item => $weight) {
            $weights->setWeight($item, $weight);
        }

        return $weights;
    }
}
