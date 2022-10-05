<?php

declare(strict_types=1);

namespace BenTools\Picker;

final class Picker
{
    /**
     * @var int
     */
    private $defaultWeight;

    /**
     * @var array
     */
    private $items = [];

    /**
     * @var bool
     */
    private $shift;

    public function __construct(int $defaultWeight = 1, bool $shift = false)
    {
        if ($defaultWeight < 0) {
            throw new \InvalidArgumentException('`defaultWeight` must be a positive integer.');
        }
        $this->defaultWeight = $defaultWeight;
        $this->shift = $shift;
    }

    public static function create(int $defaultWeight = 1, bool $shift = false): self
    {
        return new self($defaultWeight, $shift);
    }

    public function withItem($item, ?int $weight = null): self
    {
        $weight = $weight ?? $this->defaultWeight;
        if ($weight < 0) {
            throw new \InvalidArgumentException('`weight` must be a positive integer.');
        }
        $clone = clone $this;
        $clone->items[] = [$item, $weight];

        return $clone;
    }

    public function withItems(array $items): self
    {
        $clone = clone $this;
        foreach ($items as $item) {
            $clone->items[] = [$item, $this->defaultWeight];
        }

        return $clone;
    }

    public function pick()
    {
        $choices = [];
        foreach ($this->items as $index => [$item, $weight]) {
            if (0 === $weight) {
                continue;
            }
            $choices[] = \array_fill(0, $weight, $index);
        }

        $indexes = \array_merge([], ...$choices);

        if ([] === $indexes) {
            throw new \RuntimeException('Nothing to pick.');
        }

        $max = \count($indexes) - 1;
        $index = $indexes[\random_int(0, $max)];

        [$item] = $this->items[$index];

        if ($this->shift) {
            unset($this->items[$index]);
            $this->items = \array_values($this->items);
        }

        return $item;
    }
}
