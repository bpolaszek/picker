<?php

declare(strict_types=1);

namespace BenTools\Picker\ItemPicker;

use InvalidArgumentException;

use function array_values;

/**
 * @internal
 *
 * @template T
 * @extends PickerItemCollection<T>
 */
final class UniqueItemCollection extends PickerItemCollection
{
    private array $initialItems;

    public function __construct(array $items)
    {
        parent::__construct($items);
        if ([] === $this->items) {
            throw new InvalidArgumentException('UniqueItems must be initialized with at least one item.');
        }
        $this->initialItems = $items;
    }

    public function offsetGet(mixed $offset): mixed
    {

        $value = parent::offsetGet($offset);
        unset($this->items[$offset]);
        $this->items = array_values($this->items);

        if ([] === $this->items) {
            $this->items = $this->initialItems;
        }
        return $value;
    }

    public function count(): int
    {
        return count($this->items);
    }
}
