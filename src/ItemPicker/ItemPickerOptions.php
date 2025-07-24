<?php

declare(strict_types=1);

namespace BenTools\Picker\ItemPicker;

use BenTools\Picker\ItemPicker\Algorithm\Algorithm;
use BenTools\Picker\ItemPicker\Weight\NullWeightProvider;
use BenTools\Picker\ItemPicker\Weight\WeightProviderInterface;
use InvalidArgumentException;

use const PHP_INT_MAX;

final readonly class ItemPickerOptions
{
    public function __construct(
        public Algorithm $algorithm = Algorithm::RANDOM,
        public int $defaultWeight = 1,
        public bool $allowDuplicates = true,
        public int $maxLoops = PHP_INT_MAX,
        public ?int $seed = null,
        public WeightProviderInterface $weights = new NullWeightProvider(),
    ) {
        if ($this->defaultWeight < 0) {
            throw new InvalidArgumentException('Default weight must be non-negative');
        }
        if ($this->maxLoops < 0) {
            throw new InvalidArgumentException('Max loops must be non-negative');
        }
    }
}
