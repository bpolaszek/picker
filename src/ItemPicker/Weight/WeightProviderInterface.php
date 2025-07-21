<?php

declare(strict_types=1);

namespace BenTools\Picker\ItemPicker\Weight;

interface WeightProviderInterface
{
    public function getWeight(mixed $item): int|null;

    public function hasWeightedItems(): bool;
}
