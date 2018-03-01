<?php declare(strict_types=1);

namespace IgniTest\Unit\IO;

use Igni\IO\Path;
use Igni\Utils\TestCase;

class PathTest extends TestCase
{
    public function testResolve(): void
    {
        self::assertSame('otherpath/file.ext', Path::join('otherpath', './file.ext'));
        self::assertSame('one/file.ext', Path::join('one/two/three', '../../file.ext'));
        self::assertSame('one/two/file.ext', Path::join('one/two/three/four/../other.ext', '../../file.ext'));
    }
}
