<?php

declare(strict_types=1);

namespace BenTools\Picker\ItemPicker;

use function count;

/**
 * @internal
 *
 * @template T
 * @extends PickerItemCollection<T>
 */
final class StaticItemCollection extends PickerItemCollection
{
    protected int $nbItems;

    public function count(): int
    {
        return $this->nbItems ??= count($this->items);
    }
}
