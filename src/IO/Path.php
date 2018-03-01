<?php declare(strict_types=1);

namespace Igni\IO;

class Path
{
    private $path;
    private $extension;
    private $dirname;
    private $filename;
    private $isDir;
    private $isFile;

    public function __construct(string $path)
    {
        $this->path = $path;
        $info = pathinfo($path);

        $this->extension = isset($info['extension']) ? $info['extension'] : '';
        $this->dirname = $info['dirname'];
        $this->filename = $info['filename'];
        $this->isDir = is_dir($path);
        $this->isFile = is_file($path);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getDirectoryName(): string
    {
        return $this->dirname;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function isDir(): bool
    {
        return $this->isDir;
    }

    public function isFile(): bool
    {
        return $this->isFile;
    }

    public function __toString(): string
    {
        return $this->path;
    }

    public static function join(string ...$paths): string
    {
        $path = './';
        foreach ($paths as $p) {
            if ($p === '' || $p === '.') {
                continue;
            }

            // Override root path
            if ($p[0] === DIRECTORY_SEPARATOR) {
                $path = $p;
                continue;
            }

            // Join paths
            $path  .= DIRECTORY_SEPARATOR . $p;
        }

        return self::normalize($path);
    }

    /**
     * Normalizes the given path, resolving '..' and '.' segments.
     *
     * @param string $path
     * @return string
     */
    public static function normalize(string $path): string
    {
        $path = preg_replace('#' . DIRECTORY_SEPARATOR . '{2,}#', DIRECTORY_SEPARATOR, $path);

        if ($path === DIRECTORY_SEPARATOR) {
            return $path;
        }

        $result = [];
        foreach(explode(DIRECTORY_SEPARATOR, rtrim($path,DIRECTORY_SEPARATOR)) as $part) {
            if ($part === '.') {
                continue;
            }
            if ($part === '..') {
                array_pop($result);
                continue;
            }
            $result[] = $part;
        }

        return implode(DIRECTORY_SEPARATOR, $result);
    }
}
