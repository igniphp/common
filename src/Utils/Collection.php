<?php declare(strict_types=1);

namespace Igni\Utils;

use OutOfBoundsException;

class Collection implements \ArrayAccess, \Iterator, \Countable
{
    use Traversable;
    use ArrayBehaviour;

    private $elements;

    public function __construct(...$elements)
    {
        $this->elements = $elements;
    }

    /**
     * Removes element from the beginning of array
     * and returns it
     *
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->elements);
    }

    /**
     * Removes element from the end of array
     * and returns it
     *
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->elements);
    }

    public function slice(int $offset, int $length = null): Collection
    {
        return self::fromIterable(array_slice($this->elements, $offset, $length));
    }

    /**
     * Iterates through each item in collection and passes it to callback.
     * If callback returns true item is added to new collection
     * which is returned by this method.
     *
     * @param callable $callback
     * @return Collection new collection containing matching items
     */
    public function search(callable $callback): Collection
    {
        return self::fromIterable(array_filter($this->elements, $callback));
    }

    /**
     * Iterates through each item in collection and passes it to callback method.
     * If callback method returns true item is filtered out from this collection
     * and added to new collection which is returned by this method.
     *
     * @param callable $callback
     * @return Collection new collection containing filtered out values
     */
    public function reduce(callable $callback): Collection
    {
        $removed = [];
        $preserved = [];
        foreach ($this->elements as $element) {
            if ($callback($element)) {
                $removed[] = $element;
            } else {
                $preserved[] = $element;
            }
        }
        $this->elements = $preserved;
        return self::fromIterable($removed);
    }

    public function intersect(Collection $collection): Collection
    {
        return Collection::fromIterable(array_intersect($this->elements, $collection->elements));
    }

    public function diff(Collection $collection): Collection
    {
        return Collection::fromIterable(array_diff($this->elements, $collection->elements));
    }

    public function merge(Collection $collection): Collection
    {
        return Collection::fromIterable(array_merge($this->elements, $collection->elements));
    }

    /**
     * @param $element
     * @return $this|Collection
     */
    public function push($element): Collection
    {
        $this->elements[] = $element;
        return $this;
    }

    /**
     * @param $element
     * @return $this|Collection
     */
    public function unshift($element): Collection
    {
        array_unshift($this->elements, $element);
        return $this;
    }

    /**
     * Adds one or more elements at the end of collection
     *
     * @param array ...$elements
     * @return $this|Collection
     */
    public function append(...$elements): Collection
    {
        $this->elements = array_merge($this->elements, $elements);
        return $this;
    }

    /**
     * Adds one or more elements at the beginning of collection
     *
     * @param array ...$elements
     * @return $this|Collection
     */
    public function prepend(...$elements): Collection
    {
        $this->elements = array_merge($elements, $this->elements);
        return $this;
    }

    /**
     * Reverses the order of collection's elements
     * 
     * @return $this|Collection
     */
    public function reverse(): Collection
    {
        $this->elements = array_reverse($this->elements);
        return $this;
    }

    /**
     * Sorts all elements in the collection using callback function.
     * If none callback is provided default sorting behaviour is applied.
     *
     * @param callable|null $callback
     * @return $this|Collection
     */
    public function sort(callable $callback = null): Collection
    {
        if ($callback) {
            usort($this->elements, $callback);
        } else {
            sort($this->elements);
        }

        return $this;
    }

    public function unique(): Collection
    {
        return Collection::fromIterable(array_unique($this->elements));
    }

    public function offsetSet($offset, $value): void
    {
        if (null === $offset) {
            $this->elements[] = $value;
        } else {
            if (!$this->offsetExists($offset)) {
                throw new OutOfBoundsException("Invalid offset: ${offset}");
            }
            $offset = (int)$offset;
            $this->elements[$offset] = $value;
        }
    }
}
