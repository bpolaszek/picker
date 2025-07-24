<?php

declare(strict_types=1);

namespace BenTools\Picker\Tests\ItemPicker;

use BenTools\Picker\ItemPicker\UniqueItemCollection;
use InvalidArgumentException;
use OutOfBoundsException;
use RuntimeException;
use stdClass;

use function expect;

describe('UniqueItemCollection', function () {
    it('can be instantiated with an array of items', function () {
        $items = ['a', 'b', 'c'];
        $collection = new UniqueItemCollection($items);

        expect($collection)->toHaveCount(3);
    });

    it('complains when initialized with an empty array', fn () => new UniqueItemCollection([]))
        ->throws(InvalidArgumentException::class, 'UniqueItems must be initialized with at least one item.');

    it('removes items when accessed and reindexes the array', function () {
        $items = ['a', 'b', 'c'];
        $collection = new UniqueItemCollection($items);

        expect($collection)->toHaveCount(3);

        $value = $collection[0];
        expect($value)->toBe('a');
        expect($collection)->toHaveCount(2);
        expect($collection[0])->toBe('b'); // The previous 'b' at index 1 is now at index 0
        expect($collection)->toHaveCount(1);
    });

    it('resets to initial items when all items are accessed', function () {
        $items = ['a', 'b'];
        $collection = new UniqueItemCollection($items);

        expect($collection)->toHaveCount(2);

        // Get all items, which should empty the collection
        $value1 = $collection[0]; // Now contains only 'b'
        $value2 = $collection[0]; // Should be empty after this and reset to initial

        expect($value1)->toBe('a');
        expect($value2)->toBe('b');

        // Collection should be reset to initial state
        expect($collection)->toHaveCount(2);
        expect($collection[0])->toBe('a'); // Accessing again should get first item
    });

    it('can be iterated but modifies the collection during iteration', function () {
        $items = ['a', 'b', 'c', 'd'];
        $collection = new UniqueItemCollection($items);

        $result = [];
        // This is a bit tricky because the iteration will modify the collection
        // Let's manually iterate to demonstrate
        $result[] = $collection[0]; // After this, collection has 'b', 'c', 'd'
        $result[] = $collection[0]; // After this, collection has 'c', 'd'
        $result[] = $collection[0]; // After this, collection has 'd'
        $result[] = $collection[0]; // After this, collection resets to original

        expect($result)->toBe(['a', 'b', 'c', 'd']);
        expect($collection)->toHaveCount(4); // Collection reset after all items accessed
    });

    it('throws exception when trying to set an offset', function () {
        $items = ['a', 'b', 'c'];
        $collection = new UniqueItemCollection($items);

        $collection[1] = 'd';
    })->throws(RuntimeException::class, 'Cannot set offset 1.');

    it('throws exception when trying to unset an offset', function () {
        $items = ['a', 'b', 'c'];
        $collection = new UniqueItemCollection($items);

        unset($collection[1]);
    })->throws(RuntimeException::class, 'Cannot unset offset 1.');

    it('works with objects as items', function () {
        $a = new stdClass();
        $b = new stdClass();
        $c = new stdClass();

        $collection = new UniqueItemCollection([$a, $b, $c]);

        expect($collection)->toHaveCount(3);
        expect($collection[0])->toBe($a);
        expect($collection)->toHaveCount(2);
        expect($collection[0])->toBe($b);
        expect($collection)->toHaveCount(1);
    });

    it('normalizes non-zero indexed arrays', function () {
        $items = [5 => 'a', 10 => 'b', 15 => 'c'];
        $collection = new UniqueItemCollection($items);

        expect($collection)->toHaveCount(3);
        expect($collection[0])->toBe('a');
        expect($collection)->toHaveCount(2);
        expect($collection[0])->toBe('b');
        expect($collection)->toHaveCount(1);
    });

    it('maintains the count of remaining items', function () {
        $items = ['a', 'b', 'c', 'd', 'e'];
        $collection = new UniqueItemCollection($items);

        expect($collection)->toHaveCount(5);

        $collection[0]; // Remove 'a'
        expect($collection)->toHaveCount(4);

        $collection[2]; // Remove item at new index 2 (which is 'd')
        expect($collection)->toHaveCount(3);

        $collection[0]; // Remove 'b'
        $collection[0]; // Remove 'c'
        $collection[0]; // Remove 'e' and reset

        expect($collection)->toHaveCount(5); // Reset to initial count
    });

    it('correctly handles offset access after item removal', function () {
        $items = ['a', 'b', 'c', 'd', 'e'];
        $collection = new UniqueItemCollection($items);

        $collection[2]; // Remove 'c'
        expect($collection[2])->toBe('d'); // Now 'd' is at index 2 after 'c' was removed
    });

    it('throws exception when accessing non-existent offset', function () {
        $items = ['a', 'b', 'c'];
        $collection = new UniqueItemCollection($items);

        $collection[3]; // This should throw an exception
    })->throws(OutOfBoundsException::class, 'Offset 3 does not exist.');

    it('stores a copy of initial items, not a reference', function () {
        $initialArray = ['a', 'b', 'c'];
        $collection = new UniqueItemCollection($initialArray);

        // Access all items to empty the collection
        $collection[0];
        $collection[0];
        $collection[0];

        // Modify the original array
        $initialArray[0] = 'x';
        $initialArray[1] = 'y';
        $initialArray[2] = 'z';

        // Collection should still have the original values
        expect($collection[0])->toBe('a');
    });
});
