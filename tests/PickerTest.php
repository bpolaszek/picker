<?php

namespace BenTools\Picker\Tests;

use BenTools\Picker\Picker;

it('cannot have a negative default weight', function () {
    Picker::create(-1);
})->throws(
    \InvalidArgumentException::class,
    '`defaultWeight` must be a positive integer.'
);

it('can have a 0 weight', function () {
    Picker::create(0);
    expect(true)->toBe(true);
});

it('can have a positive weight', function () {
    Picker::create(10);
    expect(true)->toBe(true);
});

it('can have a default weight', function () {
    Picker::create();
    expect(true)->toBe(true);
});

it('cannot add an item with a negative weight', function () {
    Picker::create()->withItem(new \stdClass(), -1);
})->throws(
    \InvalidArgumentException::class,
    '`weight` must be a positive integer.'
);

it('can add an item with a 0 weight', function () {
    $item = new \stdClass();
    $picker = Picker::create()->withItem($item, 0);
    $picker->pick();
})->throws(\RuntimeException::class, 'Nothing to pick.');

it('can add an item with a positive weight', function () {
    $item = new \stdClass();
    $picker = Picker::create()->withItem($item, 10);
    expect($picker->pick())->toBe($item);
});

it('can add an item with a default weight', function () {
    $item = new \stdClass();
    $picker = Picker::create()->withItem($item);
    expect($picker->pick())->toBe($item);
});

it('evenly picks items', function () {
    $picker = Picker::create()
        ->withItem('foo', 500)
        ->withItem('bar', 500);

    $items = [];
    for ($i = 0; $i < 1000; $i++) {
        $items[] = $picker->pick();
    }

    $foos = \array_filter($items, function (string $item) {
        return 'foo' === $item;
    });

    $bars = \array_filter($items, function (string $item) {
        return 'bar' === $item;
    });

    expect(\count($foos))->toEqualWithDelta(500, 50);
    expect(\count($bars))->toEqualWithDelta(500, 50);
});

it('evenly picks items with an array of items', function () {
    $picker = Picker::create(500)->withItems(['foo', 'bar']);

    $items = [];
    for ($i = 0; $i < 1000; $i++) {
        $items[] = $picker->pick();
    }

    $foos = \array_filter($items, function (string $item) {
        return 'foo' === $item;
    });

    $bars = \array_filter($items, function (string $item) {
        return 'bar' === $item;
    });

    expect(\count($foos))->toEqualWithDelta(500, 50);
    expect(\count($bars))->toEqualWithDelta(500, 50);
});

it('picks more foos that bars', function () {
    $picker = Picker::create()
        ->withItem('foo', 800)
        ->withItem('bar', 200);

    $items = [];
    for ($i = 0; $i < 1000; $i++) {
        $items[] = $picker->pick();
    }

    $foos = \array_filter($items, function (string $item) {
        return 'foo' === $item;
    });

    $bars = \array_filter($items, function (string $item) {
        return 'bar' === $item;
    });

    expect(\count($foos))->toEqualWithDelta(800, 50);
    expect(\count($bars))->toEqualWithDelta(200, 50);
});
