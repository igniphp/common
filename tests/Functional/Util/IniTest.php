<?php declare(strict_types=1);

namespace IgniTest\Unit\Util;

use Igni\IO\File\Ini;
use Igni\Utils\TestCase;

class IniTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $ini = new Ini(__DIR__ . '/../../Fixtures/file.ini');

        self::assertInstanceOf(Ini::class, $ini);
    }

    public function testParse(): void
    {
        $ini = new Ini(__DIR__ . '/../../Fixtures/file.ini', [
            'a' => [
                'test' => 'a'
            ],
            'b' => [
                'test' => 'b'
            ]
        ]);
        $config = $ini->parse();

        self::assertEquals(
            [
                'section1' => [
                    'a' => 'a',
                    'b' => 'b',
                    'one' => '1',
                    'two' => '2',
                    'test' => 'a',
                ],
                'section2' => [
                    'three' => '3',
                    'test' => 'b',
                ],
                'section3' => [
                    'four' => [1, 2, 3, 4]
                ],
                'namespace' => [
                    'a' => [
                        'a' => 1,
                        'name' => 'a',
                    ],
                    'b' => [
                        'a' => 2,
                        'name' => 'b',
                    ],
                ],
            ],
            $config
        );
    }
}
