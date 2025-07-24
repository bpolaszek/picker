<?php

declare(strict_types=1);

namespace BenTools\Picker\Tests\ItemPicker;

use BenTools\Picker\ItemPicker\StaticItemCollection;
use OutOfBoundsException;
use ReflectionClass;
use RuntimeException;
use stdClass;

use function expect;

describe('StaticItemCollection', function () {
    it('can be instantiated with an array of items', function () {
        $items = ['a', 'b', 'c'];
        $collection = new StaticItemCollection($items);

        expect($collection)->toHaveCount(3);
    });

    it('can be instantiated with an iterable of items', function () {
        $items = (function () {
            yield 'a';
            yield 'b';
            yield 'c';
        })();

        $collection = new StaticItemCollection($items);

        expect($collection)->toHaveCount(3);
    });

    it('can be accessed via array access', function () {
        $items = ['a', 'b', 'c'];
        $collection = new StaticItemCollection($items);

        expect($collection[0])->toBe('a')
            ->and($collection[1])->toBe('b')
            ->and($collection[2])->toBe('c')
            ->and(isset($collection[1]))->toBeTrue()
            ->and(isset($collection[3]))->toBeFalse();
    });

    it('can be iterated', function () {
        $items = ['a', 'b', 'c'];
        $collection = new StaticItemCollection($items);

        $result = [];
        foreach ($collection as $item) {
            $result[] = $item;
        }

        expect($result)->toBe(['a', 'b', 'c']);
    });

    it('throws exception when accessing non-existent offset', function () {
        $items = ['a', 'b', 'c'];
        $collection = new StaticItemCollection($items);
        $collection[3]; // @phpstan-ignore expr.resultUnused
    })->throws(OutOfBoundsException::class, 'Offset 3 does not exist.');

    it('throws exception when trying to set an offset', function () {
        $items = ['a', 'b', 'c'];
        $collection = new StaticItemCollection($items);

        $collection[1] = 'd';
        expect(fn () => $collection[1] = 'd');
    })->throws(RuntimeException::class, 'Cannot set offset 1.');

    it('throws exception when trying to unset an offset', function () {
        $items = ['a', 'b', 'c'];
        $collection = new StaticItemCollection($items);

        unset($collection[1]);
    })->throws(RuntimeException::class, 'Cannot unset offset 1.');

    it('counts the number of items only once', function () {
        $items = ['a', 'b', 'c'];
        $collection = new StaticItemCollection($items);

        // First call calculates count
        expect($collection->count())->toBe(3);

        // Modify the protected property to test caching
        $reflection = new ReflectionClass($collection);
        $itemsProperty = $reflection->getProperty('items');
        $itemsProperty->setValue($collection, ['a', 'b', 'c', 'd']);

        // Second call should return the cached value
        expect($collection->count())->toBe(3);
    });

    it('works with objects as items', function () {
        $a = new stdClass();
        $b = new stdClass();
        $c = new stdClass();

        $collection = new StaticItemCollection([$a, $b, $c]);

        expect($collection)->toHaveCount(3)
            ->and($collection[0])->toBe($a)
            ->and($collection[1])->toBe($b)
            ->and($collection[2])->toBe($c);
    });

    it('normalizes non-zero indexed arrays', function () {
        $items = [5 => 'a', 10 => 'b', 15 => 'c'];
        $collection = new StaticItemCollection($items);

        expect($collection)->toHaveCount(3)
            ->and($collection[0])->toBe('a')
            ->and($collection[1])->toBe('b')
            ->and($collection[2])->toBe('c');
    });
});
