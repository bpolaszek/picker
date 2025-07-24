<?php

declare(strict_types=1);

namespace BenTools\Picker\ItemPicker;

use BenTools\Picker\ItemPicker\Algorithm\PickerAlgorithmInterface;
use BenTools\Picker\PickerInterface;

use function count;
use function is_array;

/**
 * @template T
 *
 * @implements PickerInterface<T>
 */
final class ItemPicker implements PickerInterface
{
    private int $currentLoop = 0;
    private int $nbPickedItems = 0;
    private int $itemsPerLoop;
    private ?int $seed = null;

    private function __construct(
        private readonly PickerItemCollection $items,
        private readonly PickerAlgorithmInterface $algorithm,
        private readonly ItemPickerOptions $options,
    ) {
        $this->itemsPerLoop = count($this->items);
        $this->seed = $options->seed;
    }

    public function pick(): mixed
    {
        if ($this->currentLoop >= $this->options->maxLoops) {
            throw new \RuntimeException('Maximum number of loops reached');
        }

        $pickedItem = $this->algorithm->pick($this->items, $this->options, $this);

        $this->nbPickedItems++;
        if (0 === ($this->nbPickedItems % $this->itemsPerLoop)) {
            $this->currentLoop++;
        }

        return $pickedItem;
    }

    public function getSeed(): ?int
    {
        return $this->seed;
    }

    public function updateSeed(): void
    {
        if (null === $this->seed) {
            return;
        }

        $this->seed++;
    }

    public static function create(
        iterable $items,
        ItemPickerOptions $options = new ItemPickerOptions(),
    ): self {
        $pickerItems = match ($options->allowDuplicates) {
            true => new StaticItemCollection(is_array($items) ? $items : [...$items]),
            false => new UniqueItemCollection(is_array($items) ? $items : [...$items]),
        };

        return new self($pickerItems, $options->algorithm->instantiate($options), $options);
    }
}
