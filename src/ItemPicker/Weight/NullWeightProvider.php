<?php

declare(strict_types=1);

namespace BenTools\Picker\ItemPicker\Weight;

/**
 * @internal
 * @codeCoverageIgnore
 */
final class NullWeightProvider implements WeightProviderInterface
{
    public function getWeight(mixed $item): null
    {
        return null;
    }

    public function hasWeightedItems(): bool
    {
        return false;
    }
}
