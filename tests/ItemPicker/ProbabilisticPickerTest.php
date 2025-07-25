<?php

declare(strict_types=1);

namespace BenTools\Picker\Tests\ItemPicker;

use BenTools\Picker\ItemPicker\Algorithm\Algorithm;
use BenTools\Picker\ItemPicker\ItemPicker;
use BenTools\Picker\ItemPicker\ItemPickerOptions;
use BenTools\Picker\ItemPicker\Weight\Weights;
use InvalidArgumentException;
use RuntimeException;
use stdClass;
use WeakMap;

use function array_chunk;
use function array_count_values;
use function array_filter;
use function array_slice;
use function expect;

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
            weights: Weights::fromAssociativeArray($weights)
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

        $weights = new \WeakMap();
        $weights[$a] = 10;
        $weights[$b] = 20;
        $weights[$c] = 30;
        $weights[$d] = 40;

        $picker = ItemPicker::create([$a, $b, $c, $d], new ItemPickerOptions(
            algorithm: Algorithm::RANDOM,
            weights: Weights::fromWeakMap($weights), // @phpstan-ignore argument.type
        ));

        $pickedItems = [];
        for ($i = 0; $i < 10_000; $i++) {
            $pickedItems[] = $picker->pick();
        }
        $total = count($pickedItems);

        // Assert items were picked in a probabilistic manner
        $counters = new \WeakMap();
        $counters[$a] = count(array_filter($pickedItems, fn ($item) => $item === $a));
        $counters[$b] = count(array_filter($pickedItems, fn ($item) => $item === $b));
        $counters[$c] = count(array_filter($pickedItems, fn ($item) => $item === $c));
        $counters[$d] = count(array_filter($pickedItems, fn ($item) => $item === $d));

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
        $items = ['a', 'b', 'c', 'd'];
        $weights = [
            'a' => 10,
            'b' => 20,
            'c' => 30,
            'd' => 40,
        ];
        $picker = ItemPicker::create($items, new ItemPickerOptions(
            algorithm: Algorithm::RANDOM,
            maxLoops: 10,
            weights: Weights::fromGenerator((fn () => yield from $weights)()),
        ));

        $pickedItems = [];
        for ($i = 0; $i < 1_000; $i++) {
            try {
                $pickedItems[] = $picker->pick();
            } catch (RuntimeException) {
                break; // Stop picking when max loops is reached
            }
        }

        // Assert items were picked in a probabilistic manner
        $counters = array_count_values($pickedItems);
        expect($counters)->toHaveKeys(['a', 'b', 'c', 'd']);

        // Assert only 40 items were picked
        $this->assertCount(40, $pickedItems);
    });

    it('returns the same random item when the seed is set', function () {
        $items = ['a', 'b', 'c', 'd'];
        $weights = [
            'a' => 10,
            'b' => 20,
            'c' => 30,
            'd' => 40,
        ];
        $picker1 = ItemPicker::create($items, new ItemPickerOptions(
            algorithm: Algorithm::RANDOM,
            seed: 123456,
            weights: Weights::fromAssociativeArray($weights),
        ));

        $picker2 = ItemPicker::create($items, new ItemPickerOptions(
            algorithm: Algorithm::RANDOM,
            seed: 123456,
            weights: Weights::fromAssociativeArray($weights),
        ));

        $picker3 = ItemPicker::create($items, new ItemPickerOptions(
            algorithm: Algorithm::RANDOM,
            seed: 67890,
            weights: Weights::fromAssociativeArray($weights),
        ));

        $pickedItemsFromPicker1 = [];
        $pickedItemsFromPicker2 = [];

        for ($i = 0; $i < 10; $i++) {
            $pickedItemsFromPicker1[] = $picker1->pick();
        }

        for ($i = 0; $i < 10; $i++) {
            $pickedItemsFromPicker2[] = $picker2->pick();
        }

        for ($i = 0; $i < 10; $i++) {
            $pickedItemsFromPicker3[] = $picker3->pick();
        }

        $this->assertSame($pickedItemsFromPicker1, $pickedItemsFromPicker2);
        $this->assertNotSame($pickedItemsFromPicker1, $pickedItemsFromPicker3);
    });

    it('avoids duplicates', function () {
        $items = ['a', 'b', 'c', 'd'];
        $weights = [
            'a' => 10,
            'b' => 20,
            'c' => 30,
            'd' => 40,
        ];
        $picker = ItemPicker::create($items, new ItemPickerOptions(
            algorithm: Algorithm::RANDOM,
            allowDuplicates: false,
            weights: Weights::fromAssociativeArray($weights),
        ));

        $pickedItems = [];
        for ($i = 0; $i < 120; $i++) {
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
        $weights = new \WeakMap();
        $weights[$a] = 10;
        $weights[$b] = 20;
        $weights[$c] = 30;

        $picker = ItemPicker::create([$a, $b, $c], new ItemPickerOptions(
            algorithm: Algorithm::RANDOM,
            allowDuplicates: false,
            weights: Weights::fromWeakMap($weights), // @phpstan-ignore argument.type
        ));
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

    it('yells if a weight is lower than 0', function () {
        $items = ['a', 'b', 'c'];
        $weights = [
            'a' => 10,
            'b' => -5, // Invalid weight
            'c' => 30,
        ];
        $picker = ItemPicker::create($items, new ItemPickerOptions(
            algorithm: Algorithm::RANDOM,
            weights: Weights::fromAssociativeArray($weights)
        ));

        expect(fn () => $picker->pick())->toThrow(InvalidArgumentException::class, 'Weight must be non-negative');
    });

    it('yells if total weight is 0', function () {
        $items = ['a', 'b', 'c'];
        $weights = [
            'a' => 0,
            'b' => 0,
            'c' => 0, // Total weight is 0
        ];
        $picker = ItemPicker::create($items, new ItemPickerOptions(
            algorithm: Algorithm::RANDOM,
            weights: Weights::fromAssociativeArray($weights)
        ));

        expect(fn () => $picker->pick())->toThrow(InvalidArgumentException::class, 'Total weight must be greater than 0');
    });

});
