<?php

declare(strict_types=1);

namespace BenTools\Picker\Tests\ItemPicker;

use BenTools\Picker\ItemPicker\Algorithm\Algorithm;
use BenTools\Picker\ItemPicker\ItemPickerOptions;
use BenTools\Picker\ItemPicker\Weight\NullWeightProvider;
use BenTools\Picker\ItemPicker\Weight\WeightProviderInterface;
use InvalidArgumentException;

use const PHP_INT_MAX;

describe('ItemPickerOptions', function () {
    it('has default values', function () {
        $options = new ItemPickerOptions();

        expect($options->algorithm)->toBe(Algorithm::RANDOM)
            ->and($options->defaultWeight)->toBe(1)
            ->and($options->allowDuplicates)->toBeTrue()
            ->and($options->maxLoops)->toBe(PHP_INT_MAX)
            ->and($options->seed)->toBeNull()
            ->and($options->weights)->toBeInstanceOf(NullWeightProvider::class);
    });

    it('accepts custom values', function () {
        $weightProvider = $this->createMock(WeightProviderInterface::class); // @phpstan-ignore method.protected
        $options = new ItemPickerOptions(
            algorithm: Algorithm::ROUND_ROBIN,
            defaultWeight: 5,
            allowDuplicates: false,
            maxLoops: 10,
            seed: 12345,
            weights: $weightProvider,
        );

        expect($options->algorithm)->toBe(Algorithm::ROUND_ROBIN)
            ->and($options->defaultWeight)->toBe(5)
            ->and($options->allowDuplicates)->toBeFalse()
            ->and($options->maxLoops)->toBe(10)
            ->and($options->seed)->toBe(12345)
            ->and($options->weights)->toBe($weightProvider);
    });

    it('throws exception for negative defaultWeight', fn () => new ItemPickerOptions(defaultWeight: -1))
        ->throws(InvalidArgumentException::class, 'Default weight must be non-negative');

    it('throws exception for negative maxLoops', fn () => new ItemPickerOptions(maxLoops: -1))
        ->throws(InvalidArgumentException::class, 'Max loops must be non-negative');

    it('allows zero as defaultWeight', function () {
        $options = new ItemPickerOptions(defaultWeight: 0);
        expect($options->defaultWeight)->toBe(0);
    });

    it('allows zero as maxLoops', function () {
        $options = new ItemPickerOptions(maxLoops: 0);
        expect($options->maxLoops)->toBe(0);
    });
});
