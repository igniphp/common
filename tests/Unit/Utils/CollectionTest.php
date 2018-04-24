<?php declare(strict_types=1);

namespace IgniTest\Unit\Utils;

use Igni\Utils\Collection;
use Igni\Utils\TestCase;

class CollectionTest extends TestCase
{
    public function testCreate()
    {
        $collection = new Collection(1, 2, 3, 4);
        self::assertInstanceOf(\Iterator::class, $collection);
        self::assertInstanceOf(\ArrayAccess::class, $collection);
    }

    public function testIterator()
    {
        $collection = new Collection(1, 2, 3, 4);
        $i = 0;
        foreach ($collection as $item) {
            self::assertEquals(++$i, $item);
        }
        self::assertSame(4, $i);
        self::assertFalse($collection->current());
        self::assertSame(4, $collection->end());
        self::assertEquals(3, $collection->previous());
        self::assertEquals(4, $collection->next());
        self::assertFalse($collection->next());
        self::assertFalse($collection->valid());
    }

    public function testPop()
    {
        $collection = new Collection(1, 2, 3, 4);
        for ($i = 4; $i > 0; $i--) {
            self::assertEquals($i, $collection->pop());
        }
        self::assertCount(0, $collection);
    }

    public function testShift()
    {
        $collection = new Collection(1, 2, 3, 4);
        $collection->shift();
        self::assertCount(3, $collection);
        self::assertEquals([2,3,4], $collection->toArray());
    }

    public function testSlice()
    {
        $collection = new Collection(1, 2, 3, 4);
        $sliced = $collection->slice(1, 2);

        self::assertCount(4, $collection);
        self::assertCount(2, $sliced);
        self::assertEquals([2, 3], $sliced->toArray());
    }

    public function testMap()
    {
        $collection = new Collection(1, 2, 3, 4);
        $collection->map(function($item) {
            return new A($item);
        });

        foreach ($collection as $item) {
            self::assertInstanceOf(A::class, $item);
        }
    }

    public function testPush()
    {
        $collection = new Collection();
        $collection->push(1);
        $collection[] = 2;

        self::assertCount(2, $collection);
    }
}

class A
{
    private $item;

    public function __construct($item)
    {
        $this->item = $item;
    }
}
