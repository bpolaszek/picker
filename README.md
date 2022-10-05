[![Latest Stable Version](https://poser.pugx.org/bentools/picker/v/stable)](https://packagist.org/packages/bentools/picker)
[![License](https://poser.pugx.org/bentools/picker/license)](https://packagist.org/packages/bentools/picker)
[![Build Status](https://img.shields.io/travis/bpolaszek/picker/master.svg?style=flat-square)](https://travis-ci.org/bpolaszek/picker)
[![Coverage Status](https://coveralls.io/repos/github/bpolaszek/picker/badge.svg?branch=master)](https://coveralls.io/github/bpolaszek/picker?branch=master)
[![Total Downloads](https://poser.pugx.org/bentools/picker/downloads)](https://packagist.org/packages/bentools/picker)

# Picker

This simple library will help you pick a random item through a collection of items (string, objects, ints, whatever),
by optionally giving them a weight.

Usage
-----

```php
use BenTools\Picker\Picker;

require_once __DIR__ . '/vendor/autoload.php';

$collection = [
    [
        'foo',
        80,
    ],
    [
        'bar',
        60,
    ],
    [
        'baz',
        5,
    ],
];

$picker = Picker::create();
foreach ($collection as $key => [$value, $weight]) {
    $picker = $picker->withItem($value, $weight);
}

echo $picker->pick(); // Will be mostly foo or bar
```

Of course you can also simply pick a random value with a simple, no-weighted set:
```php
$picker = Picker::create()->withItems(['foo', 'bar', 'baz']);
echo $picker->pick(); // Will be a truly random value between foo, bar and baz
```

### Shift

The picker can optionally shift items once they're picked:

```php
$picker = Picker::create(shift: true)->withItems(['foo', 'bar']);
$picker->pick(); // let's assume `foo` is picked
$picker->pick(); // only `bar` remains
$picker->pick(); // RuntimeException
```

Installation
------------

This library requires PHP 7.3+.

> composer require bentools/picker

Tests
-----

> ./vendor/bin/pest


See also
--------

[bentools/split-test-analyzer](https://github.com/bpolaszek/split-test-analyzer)

[bentools/cartesian-product](https://github.com/bpolaszek/cartesian-product)

[bentools/pager](https://github.com/bpolaszek/bentools-pager)
