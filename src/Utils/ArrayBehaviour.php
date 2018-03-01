<?php declare(strict_types=1);

namespace Igni\Utils;

use Igni\Exception\OutOfBoundsException;

/**
 * @property array $elements
 */
trait ArrayBehaviour
{
    public function offsetExists($offset): bool
    {
        return isset($this->elements[$offset]);
    }

    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->elements[$offset];
        }
    }

    public function offsetSet($offset, $value): void
    {
        $this->elements[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw OutOfBoundsException::forInvalidOffset($offset);
        }
        $element = $this->elements[$offset];
        unset($this->elements[$offset]);

        return $element;
    }

    public function remove($key)
    {
        return $this->offsetUnset($key);
    }

    public function set($key, $value): void
    {
        $this->offsetSet($key, $value);
    }

    public function get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * Applies the callback to all items in collection and overrides each
     * item with the result returned by the callback
     * @param callable $callback
     *
     * @return $this
     */
    public function map(callable $callback)
    {
        foreach ($this->elements as $key => &$value) {
            $value = $callback($value, $key);
        }
        return $this;
    }

    public function each(callable $callback)
    {
        foreach ($this->elements as $key => $value) {
            $callback($value, $key);
        }

        return $this;
    }

    public function contains($element): bool
    {
        return in_array($element, $this->elements, true);
    }

    public function has($key): bool
    {
        return $this->offsetExists($key);
    }

    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    public function count(): int
    {
        return count($this->elements);
    }


    public function toArray(): array
    {
        return $this->elements;
    }

    public static function fromIterable(iterable $array)
    {
        return new self(...$array);
    }
}
