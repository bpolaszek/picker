<?php

declare(strict_types=1);

namespace BenTools\Picker\Tests\ItemPicker;

use BenTools\Picker\ItemPicker\Algorithm\Algorithm;
use BenTools\Picker\ItemPicker\ItemPicker;
use BenTools\Picker\ItemPicker\ItemPickerOptions;
use BenTools\Picker\ItemPicker\Weight\Weights;

describe('Item picker with probabilistic algorithm', function () {
    it('picks items in a probabilistic fashion', function () {
        $items = ['a', 'b', 'c', 'd'];
        $weights = [
            'a' => 10,
            'b' => 20,
            'c' => 30,
            'd' => 40,
        ];
        $picker = ItemPicker::create($items, new ItemPickerOptions(
            algorithm: Algorithm::RANDOM,
            weights: Weights::fromGenerator((fn () => yield from $weights)())
        ));

        $pickedItems = [];
        for ($i = 0; $i < 10_000; $i++) {
            $pickedItems[] = $picker->pick();
        }

        // Assert items were picked in a probabilistic manner
        $counters = array_count_values($pickedItems);
        expect($counters)->toHaveKeys(['a', 'b', 'c', 'd']);
        $total = count($pickedItems);
        expect($counters['a'])->toBeGreaterThanOrEqual($total * 0.1 * 0.8)
            ->and($counters['a'])->toBeLessThanOrEqual($total * 0.1 * 1.2)
            ->and($counters['b'])->toBeGreaterThanOrEqual($total * 0.2 * 0.8)
            ->and($counters['b'])->toBeLessThanOrEqual($total * 0.2 * 1.2)
            ->and($counters['c'])->toBeGreaterThanOrEqual($total * 0.3 * 0.8)
            ->and($counters['c'])->toBeLessThanOrEqual($total * 0.3 * 1.2)
            ->and($counters['d'])->toBeGreaterThanOrEqual($total * 0.4 * 0.8)
            ->and($counters['d'])->toBeLessThanOrEqual($total * 0.4 * 1.2);

    });

    it('also works with objects', function () {
        $a = new \stdClass();
        $b = new \stdClass();
        $c = new \stdClass();
        $d = new \stdClass();

        $weights = function () use ($a, $b, $c, $d) {
            yield $a => 10;
            yield $b => 20;
            yield $c => 30;
            yield $d => 40;
        };

        $picker = ItemPicker::create([$a, $b, $c, $d], new ItemPickerOptions(
            algorithm: Algorithm::RANDOM,
            weights: Weights::fromGenerator($weights())
        ));

        $pickedItems = [];
        for ($i = 0; $i < 10_000; $i++) {
            $pickedItems[] = $picker->pick();
        }
        $total = count($pickedItems);

        // Assert items were picked in a probabilistic manner
        $counters = new \WeakMap();
        $counters[$a] = count(array_filter($pickedItems, fn($item) => $item === $a));
        $counters[$b] = count(array_filter($pickedItems, fn($item) => $item === $b));
        $counters[$c] = count(array_filter($pickedItems, fn($item) => $item === $c));
        $counters[$d] = count(array_filter($pickedItems, fn($item) => $item === $d));

        expect($counters[$a])->toBeGreaterThanOrEqual($total * 0.1 * 0.8)
            ->and($counters[$a])->toBeLessThanOrEqual($total * 0.1 * 1.2)
            ->and($counters[$b])->toBeGreaterThanOrEqual($total * 0.2 * 0.8)
            ->and($counters[$b])->toBeLessThanOrEqual($total * 0.2 * 1.2)
            ->and($counters[$c])->toBeGreaterThanOrEqual($total * 0.3 * 0.8)
            ->and($counters[$c])->toBeLessThanOrEqual($total * 0.3 * 1.2)
            ->and($counters[$d])->toBeGreaterThanOrEqual($total * 0.4 * 0.8)
            ->and($counters[$d])->toBeLessThanOrEqual($total * 0.4 * 1.2);
    });

    it('stops when max loops is reached', function () {

    });

    it('returns the same random item when the seed is set', function () {

    });

    it('avoids duplicates', function () {

    });

    it('avoid duplicates with objects', function () {

    });
});
