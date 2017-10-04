<?php

namespace BenTools\Picker;

final class Picker implements \IteratorAggregate
{
    const DEFAULT_WEIGHT = 1;

    /**
     * @var int
     */
    private $defaultWeight;

    /**
     * @var array
     */
    private $items = [];

    /**
     * Picker constructor.
     * @param int $defaultWeight
     * @throws \InvalidArgumentException
     */
    public function __construct($defaultWeight = self::DEFAULT_WEIGHT)
    {
        if ($defaultWeight < 0) {
            throw new \InvalidArgumentException('Default weight must be greater than or equal 0.');
        } elseif (!is_integer($defaultWeight)) {
            throw new \InvalidArgumentException('Default weight must be an integer.');
        }
        $this->defaultWeight = $defaultWeight;
    }

    /**
     * @param int $defaultWeight
     * @return Picker
     */
    public static function create($defaultWeight = self::DEFAULT_WEIGHT)
    {
        return new self($defaultWeight);
    }

    /**
     * @param     $key
     * @param     $value
     * @param int $weight
     * @return Picker
     * @throws \InvalidArgumentException
     */
    public function withItem($key, $value, $weight = null)
    {
        $this->validateKey($key);

        if (null === $weight) {
            $weight = $this->defaultWeight;
        }

        $this->validateWeight($weight);

        $object = clone $this;
        $object->items[$key] = [
            'v' => $value,
            'w' => $weight,
        ];
        return $object;
    }

    public function withItems(...$values)
    {
        $object = $this;
        foreach ($values as $key => $value) {
            $object = $object->withItem($key, $value);
        }
        return $object;
    }

    /**
     * @param $key
     * @return Picker
     * @throws \InvalidArgumentException
     */
    public function withoutItem($key)
    {
        $this->validateKey($key);
        if (!array_key_exists($key, $this->items)) {
            throw new \InvalidArgumentException(sprintf('Item %s not found.', $key));
        }
        $object = clone $this;
        unset($object->items[$key]);
        return $object;
    }

    /**
     * Picks a random item, considering their weights.
     */
    public function pick()
    {
        if (0 === count($this->items)) {
            throw new \RuntimeException('There is no item to pick.');
        }
        $tmp = [];
        foreach ($this->items as $key => $item) {
            $tmp = array_merge($tmp, array_fill(0, $item['w'], $key));
        }
        $cnt = count($tmp);

        if (0 === $cnt) {
            return null;
        }

        $max = $cnt - 1;
        $picked = $tmp[random_int(0, $max)];
        return $this->items[$picked]['v'];
    }

    /**
     * @return \Generator
     */
    public function getIterator()
    {
        foreach ($this->items as $key => $item) {
            yield $key => $item['v'];
        }
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return iterator_to_array($this);
    }

    /**
     * @return array
     */
    public function getWeights()
    {
        $weights = function (array $items) {
            foreach ($items as $key => $item) {
                yield $key => $item['w'];
            }
        };
        return iterator_to_array($weights($this->items));
    }

    /**
     * @param $key
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getWeightFor($key)
    {
        if (!isset($this->items[$key])) {
            throw new \InvalidArgumentException(sprintf('Item %s does not exist.', $key));
        }
        return $this->items[$key]['w'];
    }

    /**
     * $this->pick() shortcut
     */
    public function __invoke()
    {
        return $this->pick();
    }

    /**
     * @param $key
     * @throws \InvalidArgumentException
     */
    private function validateKey($key)
    {
        if (!is_scalar($key)) {
            throw new \InvalidArgumentException('Item key must be scalar.');
        }
    }

    /**
     * @param $weight
     * @throws \InvalidArgumentException
     */
    private function validateWeight($weight)
    {
        if (!is_integer($weight)) {
            throw new \InvalidArgumentException('Item weight must be an integer.');
        } elseif ($weight < 0) {
            throw new \InvalidArgumentException('Item weight must be greater than or equal 0.');
        }
    }
}
