<?php

declare(strict_types=1);

namespace BenTools\Picker\Test;

use ValueError;

use function BenTools\Picker\random_int;

describe('random_int()', function () {
    it('returns a random integer between the given min and max', function () {
        $min = 1;
        $max = 10;
        $result = random_int($min, $max);
        expect($result)->toBeGreaterThanOrEqual($min)
            ->and($result)->toBeLessThanOrEqual($max);
    });

    it('returns different integers on subsequent calls', function () {
        $min = 0;
        $max = 1_000_000_000;
        $result1 = random_int($min, $max);
        $result2 = random_int($min, $max);
        expect($result1)->not->toBe($result2);
    });

    it('returns the same random integer when a seed is provided', function () {
        $min = 0;
        $max = 1_000_000_000;
        $seed = 12345;
        $result1 = random_int($min, $max, $seed);
        $result2 = random_int($min, $max, $seed);
        expect($result1)->toBe($result2);
    });

    it('returns different integers when different seeds are provided', function () {
        $min = 0;
        $max = 1_000_000_000;
        $seed1 = 12345;
        $seed2 = 67890;
        $result1 = random_int($min, $max, $seed1);
        $result2 = random_int($min, $max, $seed2);
        expect($result1)->not->toBe($result2);
    });

    it('throws an exception if min is greater than max', function () {
        expect(fn () => random_int(10, 1))->toThrow(ValueError::class);
    });
});
