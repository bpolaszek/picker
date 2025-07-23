<?php

declare(strict_types=1);

namespace BenTools\Picker\Tests\Misc;

use BenTools\Picker\Misc\WeakMap;
use InvalidArgumentException;
use ReflectionProperty;
use stdClass;

use function gc_collect_cycles;

describe('WeakMap', function () {
    it('stores and retrieves values with object keys', function () {
        $map = new WeakMap();
        $key = new stdClass();
        $value = 'test value';

        $map[$key] = $value;

        expect($map[$key])->toBe($value);
    });

    it('stores and retrieves values with scalar keys', function () {
        $map = new WeakMap();

        $map['string_key'] = 'string value';
        $map[123] = 'integer value';
        $map[12.34] = 'float value';
        $map[true] = 'boolean value';

        expect($map['string_key'])->toBe('string value');
        expect($map[123])->toBe('integer value');
        expect($map[12.34])->toBe('float value');
        expect($map[true])->toBe('boolean value');
    });

    it('checks if an offset exists', function () {
        $map = new WeakMap();
        $key = new stdClass();

        expect(isset($map[$key]))->toBeFalse();

        $map[$key] = 'test';

        expect(isset($map[$key]))->toBeTrue();
    });

    it('removes values when unset', function () {
        $map = new WeakMap();
        $key = new stdClass();
        $map[$key] = 'test';

        unset($map[$key]);

        expect(fn () => $map[$key])->toThrow(InvalidArgumentException::class);
    });

    it('throws an exception when accessing a non-existent offset', function () {
        $map = new WeakMap();
        $key = new stdClass();

        expect(fn () => $map[$key])->toThrow(InvalidArgumentException::class, 'Invalid offset.');
    });

    it('counts the number of stored items', function () {
        $map = new WeakMap();

        expect($map->count())->toBe(0);

        $map['a'] = 1;
        $map['b'] = 2;
        $map[new stdClass()] = 3;

        expect($map->count())->toBe(3);

        unset($map['a']);

        expect($map->count())->toBe(2);
    });

    it('allows iteration over the map', function () {
        $map = new WeakMap();
        $obj = new stdClass();

        $map['a'] = 1;
        $map[$obj] = 2;
        $map['c'] = 3;

        $items = [];
        foreach ($map as $key => $value) {
            if (is_object($key)) {
                $items['object'] = $value;
            } else {
                $items[$key] = $value;
            }
        }

        expect($items)->toBe([
            'a' => 1,
            'object' => 2,
            'c' => 3,
        ]);
    });

    it('handles multiple object keys with unique identities', function () {
        $map = new WeakMap();
        $obj1 = new stdClass();
        $obj2 = new stdClass();

        $map[$obj1] = 'value 1';
        $map[$obj2] = 'value 2';

        expect($map[$obj1])->toBe('value 1');
        expect($map[$obj2])->toBe('value 2');
    });

    it('overwrites values when using the same key', function () {
        $map = new WeakMap();
        $key = new stdClass();

        $map[$key] = 'original';
        $map[$key] = 'updated';

        expect($map[$key])->toBe('updated');
    });

    it('removes entries when object keys are garbage collected', function () {
        $map = new WeakMap();

        // Create a block scope to control object lifetime
        (static function(WeakMap $map) {
            $tempObject = new stdClass();
            $map[$tempObject] = 'This value should disappear';

            // Verify the value exists while the object is in scope
            expect(isset($map[$tempObject]))->toBeTrue();
            expect($map[$tempObject])->toBe('This value should disappear');
            expect($map->count())->toBe(1);

            // Get the key we'll need to check after the object is gone
            $reflectionProperty = new ReflectionProperty($map, 'storage');
            $storage = $reflectionProperty->getValue($map);
            $keyToCheck = array_key_first($storage);

            // Return what we need outside the closure
            return [$keyToCheck, $map];
        })($map);

        // Force garbage collection
        gc_collect_cycles();

        // After the block, $tempObject should be destroyed
        // Try to iterate through the map - it should have no valid entries
        $items = 0;
        foreach ($map as $value) {
            $items++;
        }

        expect($items)->toBe(0);
    });
});
