<?php

declare(strict_types=1);

namespace BenTools\Picker\Tests;

use BenTools\Picker\ItemPicker\ItemPicker;
use BenTools\Picker\NumberPicker\NumberPicker;
use BenTools\Picker\Picker;

describe('Picker', function () {
    it('can create an ItemPicker from items', function () {
        $items = ['apple', 'banana', 'cherry'];
        $picker = Picker::fromItems($items);
        expect($picker)->toBeInstanceOf(ItemPicker::class);
    });

    it('can create a NumberPicker between two numbers', function () {
        $min = 1;
        $max = 10;
        $picker = Picker::betweenNumbers($min, $max);
        expect($picker)->toBeInstanceOf(NumberPicker::class);
    });
});
