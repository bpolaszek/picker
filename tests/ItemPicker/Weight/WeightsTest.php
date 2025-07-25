<?php

declare(strict_types=1);

namespace BenTools\Picker\Tests\ItemPicker\Weight;

use BenTools\Picker\ItemPicker\Weight\Weights;
use Generator;
use stdClass;

use function expect;
use function it;

describe('Weights', function () {

    it('starts with no weighted items', function () {
        $weights = new Weights();
        expect($weights->hasWeightedItems())->toBeFalse();
    });

    it('can set and get weights for scalar values', function () {
        $weights = new Weights();

        $weights->setWeight('item1', 10);
        $weights->setWeight('item2', 20);

        expect($weights->getWeight('item1'))->toBe(10)
            ->and($weights->getWeight('item2'))->toBe(20)
            ->and($weights->getWeight('item3'))->toBeNull()
            ->and($weights->hasWeightedItems())->toBeTrue();
    });

    it('can set and get weights for objects', function () {
        $weights = new Weights();
        $obj1 = new stdClass();
        $obj2 = new stdClass();

        $weights->setWeight($obj1, 30);
        $weights->setWeight($obj2, 40);

        expect($weights->getWeight($obj1))->toBe(30)
            ->and($weights->getWeight($obj2))->toBe(40)
            ->and($weights->getWeight(new stdClass()))->toBeNull()
            ->and($weights->hasWeightedItems())->toBeTrue();
    });

    it('can be created from a generator', function () {
        $generator = (function (): Generator {
            yield 'item1' => 10;
            yield 'item2' => 20;
            yield 'item3' => 30;
        })();

        $weights = Weights::fromGenerator($generator);

        expect($weights->getWeight('item1'))->toBe(10)
            ->and($weights->getWeight('item2'))->toBe(20)
            ->and($weights->getWeight('item3'))->toBe(30)
            ->and($weights->getWeight('item4'))->toBeNull()
            ->and($weights->hasWeightedItems())->toBeTrue();
    });

    it('can be created from a generator with object keys', function () {
        $obj1 = new stdClass();
        $obj2 = new stdClass();

        $generator = (function () use ($obj1, $obj2): Generator {
            yield $obj1 => 50;
            yield $obj2 => 60;
        })();

        $weights = Weights::fromGenerator($generator);

        expect($weights->getWeight($obj1))->toBe(50)
            ->and($weights->getWeight($obj2))->toBe(60)
            ->and($weights->getWeight(new stdClass()))->toBeNull()
            ->and($weights->hasWeightedItems())->toBeTrue();
    });

    it('correctly handles overwriting weights', function () {
        $weights = new Weights();
        $item = 'item1';

        $weights->setWeight($item, 10);
        expect($weights->getWeight($item))->toBe(10);

        $weights->setWeight($item, 20);
        expect($weights->getWeight($item))->toBe(20);
    });

    it('correctly reports having weighted items', function () {
        $weights = new Weights();
        expect($weights->hasWeightedItems())->toBeFalse();

        $weights->setWeight('item', 10);
        expect($weights->hasWeightedItems())->toBeTrue();
    });

    it('supports different types of items', function () {
        $weights = new Weights();

        $weights->setWeight('string', 10);
        $weights->setWeight(123, 20);
        $weights->setWeight(true, 30);
        $weights->setWeight(new stdClass(), 40);

        expect($weights->getWeight('string'))->toBe(10)
            ->and($weights->getWeight(123))->toBe(20)
            ->and($weights->getWeight(true))->toBe(30);
    });
});
