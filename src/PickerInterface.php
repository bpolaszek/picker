<?php

declare(strict_types=1);

namespace BenTools\Picker;

/**
 * @template T
 */
interface PickerInterface
{
    /**
     * @return T
     */
    public function pick(): mixed;
}
