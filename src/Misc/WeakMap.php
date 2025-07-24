<?php

declare(strict_types=1);

namespace BenTools\Picker\Misc;

use ArrayAccess;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use Traversable;
use WeakReference;

use function count;
use function gettype;
use function hash;
use function is_object;
use function spl_object_hash;
use function sprintf;

/**
 * A WeakMap implementation that also allows for non-object keys.
 * @internal
 */
final class WeakMap implements ArrayAccess, Countable, IteratorAggregate
{
    private array $storage = [];

    public function offsetExists(mixed $offset): bool
    {
        try {
            $this->offsetGet($offset);

            return true;
        } catch (InvalidArgumentException) {
            return false;
        }
    }

    public function offsetGet(mixed $offset): mixed
    {
        $realOffset = $this->computeKey($offset);
        [$offset, $value] = $this->storage[$realOffset]
            ?? throw new InvalidArgumentException("Invalid offset.");

        if ($offset instanceof WeakReference) {
            $object = $offset->get();
            if (null === $object) {
                unset($this->storage[$realOffset]); // @codeCoverageIgnore
                throw new InvalidArgumentException("The object has been garbage collected."); // @codeCoverageIgnore
            }
        }

        return $value;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $key = $this->computeKey($offset);

        $this->storage[$key] = [
            is_object($offset) ? WeakReference::create($offset) : $offset,
            $value,
        ];
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->storage[$this->computeKey($offset)]);
    }

    public function getIterator(): Traversable
    {
        foreach ($this->storage as [$offset, $value]) {
            if ($offset instanceof WeakReference) {
                $offset = $offset->get();
                if (null === $offset) {
                    continue; // Skip if the object has been garbage collected
                }
            }
            yield $offset => $value;
        }
    }

    public function count(): int
    {
        return count($this->storage);
    }

    private function computeKey(mixed $offset): string
    {
        $key = match (is_object($offset)) {
            true => sprintf('obj:%s', spl_object_hash($offset)),
            default => sprintf('%s:%s', gettype($offset), $offset),
        };

        return hash('xxh64', $key);
    }
}
