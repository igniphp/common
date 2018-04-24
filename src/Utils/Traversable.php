<?php declare(strict_types=1);

namespace Igni\Utils;

/**
 * Trait providing basic functionality for \Iterator interface
 *
 * @property array $elements
 */
trait Traversable
{
    public function rewind()
    {
        return reset($this->elements);
    }

    public function first()
    {
        return $this->rewind();
    }

    public function end()
    {
        return end($this->elements);
    }

    public function last()
    {
        return $this->end();
    }

    public function next()
    {
        return next($this->elements);
    }

    public function previous()
    {
        return prev($this->elements);
    }

    public function key()
    {
        return key($this->elements);
    }

    public function current()
    {
        return current($this->elements);
    }

    public function valid(): bool
    {
        return $this->key() !== null;
    }
}
