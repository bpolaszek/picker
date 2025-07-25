<?php

namespace BenTools\Picker\ItemPicker;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use OutOfBoundsException;
use RuntimeException;
use Traversable;

use function array_values;

/**
 * @template T
 * @implements ArrayAccess<int, T>
 * @implements IteratorAggregate<int, T>
 */
abstract class PickerItemCollection implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * @var T[]
     */
    protected array $items;

    /**
     * @param iterable<T> $items
     */
    public function __construct(
        iterable $items,
    ) {
        $this->items = array_values([...$items]);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * @return T
     */
    public function offsetGet(mixed $offset): mixed
    {
        if (!isset($this->items[$offset])) {
            throw new OutOfBoundsException("Offset $offset does not exist.");
        }

        return $this->items[$offset];
    }

    /**
     * @param T $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new RuntimeException("Cannot set offset $offset.");
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new RuntimeException("Cannot unset offset $offset.");
    }

    public function getIterator(): Traversable
    {
        yield from $this->items;
    }
}
