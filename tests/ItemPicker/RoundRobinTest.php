<?php

declare(strict_types=1);

namespace BenTools\Picker\Tests\ItemPicker;

use BenTools\Picker\ItemPicker\Algorithm\Algorithm;
use BenTools\Picker\ItemPicker\ItemPicker;
use BenTools\Picker\ItemPicker\ItemPickerOptions;

describe('Item picker with round-robin algorithm', function () {
    it('picks items in a round-robin fashion', function () {
        $items = ['a', 'b', 'c', 'd'];
        $options = new ItemPickerOptions(algorithm: Algorithm::ROUND_ROBIN);
        $picker = ItemPicker::create($items, $options);

        $pickedItems = [];
        for ($i = 0; $i < 8; $i++) {
            $pickedItems[] = $picker->pick();
        }

        expect($pickedItems)->toEqual(['a', 'b', 'c', 'd', 'a', 'b', 'c', 'd']);
    });

    it('also works with objects', function () {
        $a = new \stdClass();
        $b = new \stdClass();
        $c = new \stdClass();

        $picker = ItemPicker::create([$a, $b, $c], new ItemPickerOptions(algorithm: Algorithm::ROUND_ROBIN));
        $pickedItems = [];
        for ($i = 0; $i < 6; $i++) {
            $pickedItems[] = $picker->pick();
        }
        expect($pickedItems)->toEqual([$a, $b, $c, $a, $b, $c]);
    });

    it('stops when max loops is reached', function () {
        $items = ['a', 'b', 'c'];
        $options = new ItemPickerOptions(algorithm: Algorithm::ROUND_ROBIN, maxLoops: 2);
        $picker = ItemPicker::create($items, $options);

        $pickedItems = [];
        for ($i = 0; $i < 10; $i++) {
            try {
                $pickedItems[] = $picker->pick();
            } catch (\RuntimeException $e) {
                break; // Stop picking when max loops is reached
            }
        }

        expect($pickedItems)->toEqual(['a', 'b', 'c', 'a', 'b', 'c']);
    });
});
