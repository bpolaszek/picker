<?php

declare(strict_types=1);

namespace BenTools\Picker\ItemPicker;

use BenTools\Picker\ItemPicker\Algorithm\PickerAlgorithmInterface;
use BenTools\Picker\PickerInterface;

use function count;

/**
 * @template T
 *
 * @implements PickerInterface<T>
 */
final class ItemPicker implements PickerInterface
{
    public int $currentLoop = 0;
    public int $nbPickedItems = 0;
    public int $itemsPerLoop;

    private function __construct(
        private readonly PickerItemCollection $items,
        private readonly PickerAlgorithmInterface $algorithm,
        private readonly ItemPickerOptions $options,
    ) {
        $this->itemsPerLoop = count($this->items);
    }

    public function pick(): mixed
    {
        if ($this->currentLoop >= $this->options->maxLoops) {
            throw new \RuntimeException('Maximum number of loops reached');
        }

        $pickedItem = $this->algorithm->pick($this->items, $this->options);

        $this->nbPickedItems++;
        if (0 === ($this->nbPickedItems % $this->itemsPerLoop)) {
            $this->currentLoop++;
        }

        return $pickedItem;
    }

    public static function create(
        iterable $items,
        ItemPickerOptions $options = new ItemPickerOptions(),
    ): self {
        $pickerItems = match ($options->allowDuplicates) {
            true => new StaticItemCollection($items),
            false => new UniqueItemCollection($items),
        };

        return new self($pickerItems, $options->algorithm->instantiate($options), $options);
    }
}
