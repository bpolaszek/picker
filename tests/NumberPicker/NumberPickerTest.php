<?php

declare(strict_types=1);

namespace BenTools\Picker\Tests\NumberPicker;

use BenTools\Picker\NumberPicker\NumberPicker;
use BenTools\Picker\NumberPicker\NumberPickerOptions;
use ValueError;

describe('NumberPicker::pick()', function () {
    it('returns a random integer between the given min and max', function () {
        $min = 1;
        $max = 10;
        $picker = NumberPicker::create($min, $max);
        $result = $picker->pick();
        expect($result)->toBeGreaterThanOrEqual($min)
            ->and($result)->toBeLessThanOrEqual($max);
    });

    it('returns different integers on subsequent calls', function () {
        $min = 0;
        $max = 1_000_000_000;
        $picker = NumberPicker::create($min, $max);
        $result1 = $picker->pick();
        $result2 = $picker->pick();
        expect($result1)->not->toBe($result2);
    });

    it('returns the same random integer when a seed is provided', function () {
        $min = 0;
        $max = 1_000_000_000;
        $seed = 12345;
        $options = new NumberPickerOptions(seed: $seed);
        $picker1 = NumberPicker::create($min, $max, $options);
        $picker2 = NumberPicker::create($min, $max, $options);
        $result1 = $picker1->pick();
        $result2 = $picker2->pick();
        $result3 = $picker1->pick();
        $result4 = $picker2->pick();

        expect($result1)->toBe($result2)
            ->and($result3)->toBe($result4)
            ->and($result1)->not->toBe($result3)
            ->and($result2)->not->toBe($result4)
        ;
    });

    it('returns different integers when different seeds are provided', function () {
        $min = 0;
        $max = 1_000_000_000;
        $seed1 = 12345;
        $seed2 = 67890;
        $options1 = new NumberPickerOptions(seed: $seed1);
        $options2 = new NumberPickerOptions(seed: $seed2);
        $picker1 = NumberPicker::create($min, $max, $options1);
        $picker2 = NumberPicker::create($min, $max, $options2);
        $result1 = $picker1->pick();
        $result2 = $picker2->pick();
        expect($result1)->not->toBe($result2);
    });
});

describe('NumberPicker::randomInt()', function () {
    it('returns a random integer between the given min and max', function () {
        $min = 1;
        $max = 1_000_000_000;
        $result = NumberPicker::randomInt($min, $max);
        expect($result)->toBeGreaterThanOrEqual($min)
            ->and($result)->toBeLessThanOrEqual($max);
    });

    it('returns different integers on subsequent calls', function () {
        $min = 0;
        $max = 1_000_000_000;
        $result1 = NumberPicker::randomInt($min, $max);
        $result2 = NumberPicker::randomInt($min, $max);
        expect($result1)->not->toBe($result2);
    });

    it('returns the same random integer when a seed is provided', function () {
        $min = 0;
        $max = 1_000_000_000;
        $seed = 12345;
        $result1 = NumberPicker::randomInt($min, $max, $seed);
        $result2 = NumberPicker::randomInt($min, $max, $seed);
        expect($result1)->toBe($result2);
    });

    it('returns different integers when different seeds are provided', function () {
        $min = 0;
        $max = 1_000_000_000;
        $seed1 = 12345;
        $seed2 = 67890;
        $result1 = NumberPicker::randomInt($min, $max, $seed1);
        $result2 = NumberPicker::randomInt($min, $max, $seed2);
        expect($result1)->not->toBe($result2);
    });

    it('throws an exception if min is greater than max', function (int $min, int $max, ?int $seed = null) {
        return NumberPicker::randomInt($min, $max, $seed);
    })
        ->throws(ValueError::class)
        ->with(function () {
            yield 'unseeded picker' => ['min' => 10, 'max' => 1];
            yield 'seeded picker' => ['min' => 10, 'max' => 1, 'seed' => 123456];
        })
    ;
});

