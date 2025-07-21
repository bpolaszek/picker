<?php

declare(strict_types=1);

namespace BenTools\Picker\ItemPicker\Algorithm;

use BenTools\Picker\ItemPicker\ItemPickerOptions;
use BenTools\Picker\ItemPicker\PickerItemCollection;

interface PickerAlgorithmInterface
{
    /**
     * @template T
     * @param PickerItemCollection<T> $items
     *
     * @return T
     */
    public function pick(PickerItemCollection $items, ItemPickerOptions $options): mixed;
}
