<?php declare(strict_types=1);

namespace IgniTest\Unit\Util;

use Igni\Utils\Enum;
use Igni\Utils\TestCase;

class EnumTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $enum = new EnumA('a');
        self::assertInstanceOf(Enum::class, $enum);
        self::assertSame(EnumA::A, $enum->value());

        $enum = EnumA::B();
        self::assertInstanceOf(Enum::class, $enum);
        self::assertSame(EnumA::B, $enum->value());
    }

    public function testValues(): void
    {
        $values = EnumA::values();
        self::assertSame(['a', 'b', 'c'], $values);
    }

    public function testKeys(): void
    {
        $keys = EnumA::keys();
        self::assertSame(['A', 'B', 'C'], $keys);
    }
}

class EnumA extends Enum
{
    public const A = 'a';
    public const B = 'b';
    public const C = 'c';
}
