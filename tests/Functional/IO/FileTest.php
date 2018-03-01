<?php declare(strict_types=1);

namespace IgniTestFunctional\System;

use Igni\IO\File;
use Igni\Utils\TestCase;

class FileTest extends TestCase
{
    public function testCreateTemporary(): void
    {
        $file = File::createTemporary();
        $file->write("Data");
        $file->rewind();

        self::assertSame('Data', $file->read());
    }

    public function testReadWithoutArgument(): void
    {
        $filename = __DIR__ . '/../../Fixtures/file.ini';
        $file = new File($filename);

        self::assertSame(file_get_contents($filename), $file->read());
        $file->close();
    }

    public function testReadWithLength(): void
    {
        $filename = __DIR__ . '/../../Fixtures/file.ini';
        $file = new File($filename);

        $content = $file->read(9);
        self::assertSame('; Example', $content);

        $content = $file->read(10);
        self::assertSame(' ini file' . PHP_EOL, $content);

        $file->close();
    }

    public function testSize(): void
    {
        $filename = __DIR__ . '/../../Fixtures/file.ini';
        $file = new File($filename);

        self::assertSame(filesize($filename), $file->size());
        $file->close();
    }

    public function testRename(): void
    {
        $dirname = __DIR__ . '/../../Fixtures';

        $file = new File($dirname . '/file.ini');
        $file->rename('moved.ini');

        self::assertTrue(file_exists($dirname . '/moved.ini'));

        $file->rename('file.ini');

        self::assertTrue(file_exists($dirname . '/file.ini'));

        $file->close();
    }

    public function testCopyAndDelete(): void
    {
        $dirname = __DIR__ . '/../../Fixtures';
        $file = new File($dirname . '/file.ini');
        $copy = $file->copy('copied.ini');

        self::assertInstanceOf(File::class, $copy);

        self::assertSame(file_get_contents($dirname . '/file.ini'), file_get_contents($dirname . '/copied.ini'));

        $copy->delete();
        $file->close();

        self::assertFalse(file_exists((string) $copy->getPath()));
    }
}
