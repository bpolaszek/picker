<?php

declare(strict_types=1);

namespace BenTools\Picker\ItemPicker\Algorithm;

use BenTools\Picker\ItemPicker\ItemPicker;
use BenTools\Picker\ItemPicker\ItemPickerOptions;
use BenTools\Picker\ItemPicker\PickerItemCollection;

interface PickerAlgorithmInterface
{
    /**
     * @template T
     * @param PickerItemCollection<T> $items
     * @param ItemPicker<T> $picker
     *
     * @return T
     */
    public function pick(PickerItemCollection $items, ItemPickerOptions $options, ItemPicker $picker): mixed;
}
