<?php declare(strict_types=1);

namespace Igni\IO;

use Igni\Exception\InvalidArgumentException;
use Igni\IO\Exception\FileException;
use Igni\Utils\ReflectionApi;
use Generator;

class File
{
    private $mode;
    private $handle;
    private $filename;
    private $path;
    private $metaData;

    public function __construct(string $filename, string $mode = 'r')
    {
        $this->mode = $mode;
        $this->filename = Path::join(getcwd(), $filename);
        $this->path = new Path($filename);
        if ($this->path->isDir()) {
            throw new InvalidArgumentException("Path `{$this->filename}` is not a valid filename.");
        }
    }

    public function exists(): bool
    {
        return file_exists($this->filename);
    }

    public function isReadable(): bool
    {
        return is_readable($this->filename);
    }

    public function isWritable(): bool
    {
        return is_writable($this->filename);
    }

    public function open(): void
    {
        if ($this->handle) {
            return;
        }

        $this->handle = fopen($this->filename, $this->mode);
        if ($this->handle === false) {
            throw new InvalidArgumentException("Could not open file `{$this->filename}` for given mode `{$this->mode}`");
        }

        $this->metaData = stream_get_meta_data($this->handle);
    }

    public function close(): void
    {
        if ($this->handle) {
            fclose($this->handle);
            $this->handle = null;
        }
    }

    public function readLines(): Generator
    {
        $this->open();
        $this->rewind();
        while (($line = fgets($this->handle)) !== false) {
            yield $line;
        }
        $this->close();
    }

    public function readLine(): string
    {
        $this->open();

        $line = fgets($this->handle);

        if ($line === false) {
            if (feof($this->handle)) {
                throw FileException::forEndOfFile($this->filename);
            }
            throw FileException::forUnreadableFile($this->filename);
        }

        return $line;
    }

    public function read(int $bytes = 0): string
    {
        $this->open();

        if ($bytes === 0) {
            $result = stream_get_contents($this->handle);
        } else {
            $result = fread($this->handle, $bytes);
        }

        if ($result === false) {
            if (feof($this->handle)) {
                throw FileException::forEndOfFile($this->filename);
            }
            throw FileException::forUnreadableFile($this->filename);
        }

        return $result;
    }

    public function write(string $data): bool
    {
        $this->open();

        $result = fwrite($this->handle, $data);

        return $result !== false;
    }

    public function rename(string $path): bool
    {
        $path = Path::join($this->path->getDirectoryName(), $path);

        if (is_uploaded_file($this->filename)) {
            return move_uploaded_file($this->filename, $path);
        }

        $result = rename($this->filename, $path);

        if ($result) {
            $this->filename = $path;
        }

        return $result;
    }

    public function copy(string $path): File
    {
        $path = Path::join($this->path->getDirectoryName(), $path);

        $this->open();
        $destination = fopen($path,'w+');
        stream_copy_to_stream($this->handle, $destination);
        fclose($destination);
        $this->close();

        return new File($path);
    }

    public function seek(int $offset): void
    {
        $this->open();
        if (!$this->metaData['seekable']) {
            throw new FileException("This stream {$this->filename} is not seekable.");
        }
        fseek($this->handle, $offset, SEEK_SET);
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function cursor(): int
    {
        $this->open();
        $position = ftell($this->handle);

        if ($position === false) {
            throw new FileException("Could not get current file pointer position.");
        }

        return $position;
    }

    public function size(): int
    {
        $size = filesize($this->filename);

        if ($size === false) {
            throw new InvalidArgumentException("Could not read size of file `{$this->filename}`");
        }

        return $size;
    }

    public function end(): bool
    {
        $this->open();
        return feof($this->handle);
    }

    public function delete(): bool
    {
        if ($this->handle) {
            $this->close();
        }

        return unlink($this->filename);
    }

    public function getPath(): Path
    {
        return $this->path;
    }

    public function __destruct()
    {
        $this->close();
    }

    public static function createTemporary(): File
    {
        /** @var File $file */
        $file = ReflectionApi::createInstance(self::class);
        $file->handle = tmpfile();
        $file->metaData = stream_get_meta_data($file->handle);
        $file->mode = $file->metaData['mode'];
        $file->path = new Path($file->metaData['uri']);
        $file->filename = $file->metaData['uri'];

        return $file;
    }
}
