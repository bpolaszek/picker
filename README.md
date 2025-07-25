[![Latest Stable Version](https://poser.pugx.org/bentools/picker/v/stable)](https://packagist.org/packages/bentools/picker)
[![License](https://poser.pugx.org/bentools/picker/license)](https://packagist.org/packages/bentools/picker)
[![Build Status](https://img.shields.io/travis/bpolaszek/picker/master.svg?style=flat-square)](https://travis-ci.org/bpolaszek/picker)
[![Coverage Status](https://coveralls.io/repos/github/bpolaszek/picker/badge.svg?branch=master)](https://coveralls.io/github/bpolaszek/picker?branch=master)
[![Total Downloads](https://poser.pugx.org/bentools/picker/downloads)](https://packagist.org/packages/bentools/picker)

# Picker

A PHP library for randomly picking items from collections and generating random numbers with advanced options like seeding, weighting, and more.

## Features

- Pick random items from collections
- Generate random numbers with optional seeding
- Support for weighted item selection
- Option to allow or prevent duplicates in selections
- Consistent results with seeded randomization

## Quick Start

### Picking Items from a Collection

#### Basic usage
```php
use BenTools\Picker\Picker;

$items = ['apple', 'banana', 'cherry'];
$picker = Picker::fromItems($items);
$randomItem = $picker->pick(); // Returns a random item from the array
```

#### Prevent duplicates

It will avoid, as much as possible, picking the same item twice in a row. 

If all items have been picked, it will cycle through them again.

```php
use BenTools\Picker\Picker;
use BenTools\Picker\ItemPicker\ItemPickerOptions;

$options = new ItemPickerOptions(allowDuplicates: false);
$picker = Picker::fromItems($items, $options);
$randomItem = $picker->pick(); // Will cycle through all items before repeating
```

### Direct Number Generation

```php
use function BenTools\Picker\random_int;

// Alternative to PHP's built-in random_int with optional seeding
$randomNumber = random_int(1, 100); // Behaves like PHP's random_int()
$seededNumber = random_int(1, 100, 12345); // Deterministic output for given seed
```

## Advanced Usage

### Item Picker Options

```php
use BenTools\Picker\Picker;
use BenTools\Picker\ItemPicker\ItemPickerOptions;
use BenTools\Picker\ItemPicker\Algorithm\Algorithm;

$options = new ItemPickerOptions(
    algorithm: Algorithm::RANDOM, // Selection algorithm
    defaultWeight: 1,             // Default weight for items
    allowDuplicates: true,        // Whether to allow the same item to be picked multiple times
    maxLoops: PHP_INT_MAX,        // Maximum number of times to loop through all items
    seed: 12345,                  // Optional seed for reproducible results
    // weights: $customWeightProvider // Custom weight provider implementation
);

$picker = Picker::fromItems(['apple', 'banana', 'cherry'], $options);
```


## Use Cases

- Randomizing elements in games
- Implementing A/B testing
- Creating fair selection mechanisms
- Generating random but reproducible test data
- Implementing weighted random selection for various algorithms

Installation
------------

This library requires PHP 8.2+.

> composer require bentools/picker:3.x-dev

Tests
-----

> ./vendor/bin/pest


See also
--------

[bentools/split-test-analyzer](https://github.com/bpolaszek/split-test-analyzer)

[bentools/cartesian-product](https://github.com/bpolaszek/cartesian-product)

