<?php

namespace BenTools\Picker\Tests;

use BenTools\Picker\Picker;
use PHPUnit\Framework\TestCase;

class PickerTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Default weight must be greater than or equal 0.
     */
    public function testNegativeDefaultWeight()
    {
        new Picker(-1);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Default weight must be an integer.
     */
    public function testNotIntegerDefaultWeight()
    {
        new Picker(5.8);
    }

    public function testAddItemWithDefaultWeight()
    {
        $picker = Picker::create(10)->withItem('foo', 'bar');
        $this->assertEquals(10, $picker->getWeightFor('foo'));
    }

    public function testAddItemWithSpecificWeight()
    {
        $picker = Picker::create(10)->withItem('foo', 'bar', 15);
        $this->assertEquals(15, $picker->getWeightFor('foo'));
    }

    /**
     * @expectedException  \InvalidArgumentException
     * @expectedExceptionMessage Item key must be scalar.
     */
    public function testAddItemWithInvalidKey()
    {
        Picker::create()->withItem(new \stdClass(), 'bar');
    }

    /**
     * @expectedException  \InvalidArgumentException
     * @expectedExceptionMessage Item weight must be an integer.
     */
    public function testAddItemWithNonIntegerWeight()
    {
        Picker::create()->withItem('foo', 'bar', 10.2);
    }

    /**
     * @expectedException  \InvalidArgumentException
     * @expectedExceptionMessage Item weight must be greater than or equal 0.
     */
    public function testAddItemWithNegativeWeight()
    {
        Picker::create()->withItem('foo', 'bar', -50);
    }

    public function testGetItems()
    {
        $picker = Picker::create()->withItem('foo', 'bar')->withItem('bar', 'baz');
        $this->assertEquals(['foo' => 'bar', 'bar' => 'baz'], $picker->getItems());
        $picker = $picker->withoutItem('foo');
        $this->assertEquals(['bar' => 'baz'], $picker->getItems());
    }

    public function testGetWeights()
    {
        $picker = Picker::create(15)->withItem('foo', 'bar')->withItem('bar', 'baz', 20);
        $this->assertEquals(['foo' => 15, 'bar' => 20], $picker->getWeights());
        $picker = $picker->withoutItem('foo');
        $this->assertEquals(['bar' => 20], $picker->getWeights());
    }

    public function testPick()
    {
        $picker = Picker::create(15)->withItem('foo', 'bar')->withItem('bar', 'baz', 20);
        for ($data = [], $i = 1; $i <= 100; $i++) {
            $data[] = $picker->pick();
        }
        $this->assertContains('bar', $data);
        $this->assertContains('baz', $data);
    }

    public function testPickWithDisabledItem()
    {
        $picker = Picker::create(15)->withItem('foo', 'bar')->withItem('bar', 'baz', 0);
        for ($data = [], $i = 1; $i <= 100; $i++) {
            $data[] = $picker->pick();
        }
        $this->assertContains('bar', $data);
        $this->assertNotContains('baz', $data);
    }

    public function testPickWithNothingEnabled()
    {
        $picker = Picker::create(15)->withItem('foo', 'bar', 0)->withItem('bar', 'baz', 0);
        for ($data = [], $i = 1; $i <= 100; $i++) {
            $data[] = $picker();
        }
        $this->assertNotContains('bar', $data);
        $this->assertNotContains('baz', $data);
        $this->assertEquals(array_fill(0, 100, null), $data);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage There is no item to pick.
     */
    public function testPickWithNoItem()
    {
        Picker::create()->pick();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Item bar not found.
     */
    public function testRemoveNotExistingItem()
    {
        Picker::create()->withoutItem('bar');
    }

    public function testRandomness()
    {
        $picker = Picker::create(15)->withItem(0, 'foo', 20)->withItem(1, 'bar', 80);
        for ($foos = 0, $bars = 0, $i = 1; $i <= 100; $i++) {
            $value = $picker->pick();
            if ('foo' === $value) {
                $foos++;
            } elseif ('bar' === $value) {
                $bars++;
            }
        }
        $this->assertEqualsApproximatively(20, $foos, 10);
        $this->assertEqualsApproximatively(80, $bars, 10);
    }
    
    private function assertEqualsApproximatively($expected, $value, $tolerance)
    {
        $left = $expected - $tolerance;
        $right = $expected + $tolerance;
        $this->assertGreaterThanOrEqual($left, $value);
        $this->assertLessThanOrEqual($right, $value);
    }
}
