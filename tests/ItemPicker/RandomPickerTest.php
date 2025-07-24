<?php

declare(strict_types=1);

namespace BenTools\Picker\Tests\ItemPicker;

use BenTools\Picker\ItemPicker\Algorithm\Algorithm;
use BenTools\Picker\ItemPicker\ItemPicker;
use BenTools\Picker\ItemPicker\ItemPickerOptions;
use RuntimeException;
use stdClass;
use WeakMap;

use function array_chunk;
use function array_count_values;
use function array_filter;
use function array_slice;
use function count;
use function expect;

describe('Item picker with random algorithm', function () {
    it('picks items in a random fashion', function () {
        $items = ['a', 'b', 'c', 'd'];
        $picker = ItemPicker::create($items, new ItemPickerOptions(algorithm: Algorithm::RANDOM));

        $pickedItems = [];
        for ($i = 0; $i < 10_000; $i++) {
            $pickedItems[] = $picker->pick();
        }

        // Assert items were picked in random order
        expect(array_slice($pickedItems, 0, 8))
            ->not()->toBe(['a', 'b', 'c', 'd', 'a', 'b', 'c', 'd']);

        // Assert all items have been picked
        $counters = array_count_values($pickedItems);
        expect($counters)->toHaveKeys(['a', 'b', 'c', 'd']);

        // Assert all counters are pretty close to each other
        $total = count($pickedItems);
        foreach ($counters as $count) {
            expect($count)->toBeGreaterThanOrEqual($total / 4 * 0.8); // At least 80% of the expected count
            expect($count)->toBeLessThanOrEqual($total / 4 * 1.2); // At most 120% of the expected count
        }
    });

    it('also works with objects', function () {
        $a = new stdClass();
        $b = new stdClass();
        $c = new stdClass();

        $picker = ItemPicker::create([$a, $b, $c], new ItemPickerOptions(algorithm: Algorithm::RANDOM));
        $pickedItems = [];
        for ($i = 0; $i < 10_000; $i++) {
            $pickedItems[] = $picker->pick();
        }

        // Assert items were picked in random order
        expect(array_slice($pickedItems, 0, 9))
            ->not()->toBe([$a, $b, $c, $a, $b, $c, $a, $b, $c]);

        // Assert all items have been picked
        $counters = new WeakMap();
        $counters[$a] = count(array_filter($pickedItems, fn ($item) => $item === $a));
        $counters[$b] = count(array_filter($pickedItems, fn ($item) => $item === $b));
        $counters[$c] = count(array_filter($pickedItems, fn ($item) => $item === $c));
        expect($counters[$a])->toBeGreaterThan(0)
            ->and($counters[$b])->toBeGreaterThan(0)
            ->and($counters[$c])->toBeGreaterThan(0);

        // Assert all counters are pretty close to each other
        $total = count($pickedItems);
        foreach ($counters as $count) {
            expect($count)->toBeGreaterThanOrEqual($total / 3 * 0.8); // At least 80% of the expected count
            expect($count)->toBeLessThanOrEqual($total / 3 * 1.2); // At most 120% of the expected count
        }
    });

    it('stops when max loops is reached', function () {
        $items = ['a', 'b', 'c'];
        $options = new ItemPickerOptions(algorithm: Algorithm::RANDOM, maxLoops: 10);
        $picker = ItemPicker::create($items, $options);

        $pickedItems = [];
        for ($i = 0; $i < 1_000; $i++) {
            try {
                $pickedItems[] = $picker->pick();
            } catch (RuntimeException) {
                break; // Stop picking when max loops is reached
            }
        }

        // Assert only 30 items were picked
        $this->assertCount(30, $pickedItems);

        // Assert all items have been picked
        $counters = array_count_values($pickedItems);
        expect($counters)->toHaveKeys(['a', 'b', 'c']);
    });

    it('returns the same random item when the seed is set', function () {
        $items = ['a', 'b', 'c', 'd'];
        $seed = 12345;
        $picker1 = ItemPicker::create($items, new ItemPickerOptions(algorithm: Algorithm::RANDOM, seed: $seed));
        $picker2 = ItemPicker::create($items, new ItemPickerOptions(algorithm: Algorithm::RANDOM, seed: $seed));

        $result1 = $picker1->pick();
        $result2 = $picker2->pick();
        $result3 = $picker1->pick();
        $result4 = $picker2->pick();

        expect($result1)->toBe($result2)
            ->and($result3)->toBe($result4)
            ->and($result1)->not->toBe($result3); // Different picks on subsequent calls
    });

    it('avoids duplicates', function () {
        $items = ['a', 'b', 'c', 'd'];
        $picker = ItemPicker::create($items, new ItemPickerOptions(algorithm: Algorithm::RANDOM, allowDuplicates: false));

        $pickedItems = [];
        for ($i = 0; $i < 12_000; $i++) {
            $pickedItems[] = $picker->pick();
        }

        // Assert items were picked in random order
        expect(array_slice($pickedItems, 0, 8))
            ->not()->toBe(['a', 'b', 'c', 'd', 'a', 'b', 'c', 'd']);

        // Assert all items have been picked and no duplicates
        foreach (array_chunk($pickedItems, 4) as $chunk) {
            expect(array_count_values($chunk))->toHaveKeys(['a', 'b', 'c', 'd']);
        }
    });

    it('avoid duplicates with objects', function () {
        $a = new stdClass();
        $b = new stdClass();
        $c = new stdClass();

        $picker = ItemPicker::create([$a, $b, $c], new ItemPickerOptions(algorithm: Algorithm::RANDOM, allowDuplicates: false));
        $pickedItems = [];
        for ($i = 0; $i < 12_000; $i++) {
            $pickedItems[] = $picker->pick();
        }

        // Assert items were picked in random order
        expect(array_slice($pickedItems, 0, 9))
            ->not()->toBe([$a, $b, $c, $a, $b, $c, $a, $b, $c]);

        // Assert all items have been picked and no duplicates
        foreach (array_chunk($pickedItems, 3) as $chunk) {
            $counters = new WeakMap();
            $counters[$a] = count(array_filter($chunk, fn ($item) => $item === $a));
            $counters[$b] = count(array_filter($chunk, fn ($item) => $item === $b));
            $counters[$c] = count(array_filter($chunk, fn ($item) => $item === $c));
            expect($counters[$a])->toEqual(1)
                ->and($counters[$b])->toEqual(1)
                ->and($counters[$c])->toEqual(1);
        }
    });
});
