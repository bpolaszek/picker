[![Latest Stable Version](https://poser.pugx.org/bentools/picker/v/stable)](https://packagist.org/packages/bentools/picker)
[![License](https://poser.pugx.org/bentools/picker/license)](https://packagist.org/packages/bentools/picker)
[![Build Status](https://img.shields.io/travis/bpolaszek/picker/master.svg?style=flat-square)](https://travis-ci.org/bpolaszek/picker)
[![Coverage Status](https://coveralls.io/repos/github/bpolaszek/picker/badge.svg?branch=master)](https://coveralls.io/github/bpolaszek/picker?branch=master)
[![Quality Score](https://img.shields.io/scrutinizer/g/bpolaszek/picker.svg?style=flat-square)](https://scrutinizer-ci.com/g/bpolaszek/picker)
[![Total Downloads](https://poser.pugx.org/bentools/picker/downloads)](https://packagist.org/packages/bentools/picker)

# Picker

Let's say you have a collection of objects / values / things / whatever. 

You want to pick a random value, but with a weight management.

Here it is.

Usage
-----

```php
use BenTools\Picker\Picker;

require_once __DIR__ . '/vendor/autoload.php';

$collection = [
    [
        'value'  => 'foo',
        'weight' => 80,
    ],
    [
        'value'  => 'bar',
        'weight' => 60,
    ],
    [
        'value'  => 'baz',
        'weight' => 5,
    ],
];

$picker = Picker::create();
foreach ($collection as $key => $value) {
    $picker = $picker->withItem($key, $value['value'], $value['weight']);
}

echo $picker->pick(); // Will be mostly foo or bar
```

Installation
------------

This library requires PHP 5.6+.

> composer require bentools/picker

Tests
-----

> ./vendor/bin/phpunit


See also
--------

[bentools/split-test-analyzer](https://github.com/bpolaszek/split-test-analyzer)

[bentools/cartesian-product](https://github.com/bpolaszek/cartesian-product)

[bentools/pager](https://github.com/bpolaszek/bentools-pager)