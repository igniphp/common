<?php declare(strict_types=1);

namespace Igni\IO\Exception;

class FileException extends IOException
{
    public static function forEndOfFile(string $file): EndOfFileException
    {
        return new class("End of file ${file}") extends FileException implements EndOfFileException {};
    }

    public static function forUnreadableFile(string $file): self
    {
        return new self("Could not read file ${file}");
    }
}
