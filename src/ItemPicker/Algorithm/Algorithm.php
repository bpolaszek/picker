<?php

declare(strict_types=1);

namespace BenTools\Picker\ItemPicker\Algorithm;

use BenTools\Picker\ItemPicker\ItemPickerOptions;

enum Algorithm
{
    case ROUND_ROBIN;
    case RANDOM;

    public function instantiate(ItemPickerOptions $options): PickerAlgorithmInterface
    {
        return match ($this) {
            self::ROUND_ROBIN => new RoundRobinAlgorithm(),
            self::RANDOM => match ($options->weights->hasWeightedItems()) {
                true => new ProbabilisticAlgorithm(),
                false => new RandomAlgorithm(),
            }
        };
    }
}
