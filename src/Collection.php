<?php

declare(strict_types=1);

namespace SlickSky\SpamBlacklistQuery;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

use function array_map;
use function count;

class Collection implements Countable, IteratorAggregate, ArrayAccess, JsonSerializable
{
    public function __construct(protected array $items)
    {
    }

    public function map(callable $fn): self
    {
        return new static(array_map($fn, $this->items));
    }

    public function offsetUnset($key): void
    {
        unset($this->items[$key]);
    }

    public function offsetGet($key): mixed
    {
        return $this->items[$key];
    }

    public function offsetExists(mixed $key): bool
    {
        return isset($this->items[$key]);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function offsetSet($key, $value): void
    {
        if ($key === null) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
