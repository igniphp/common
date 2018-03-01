<?php declare(strict_types=1);

namespace Igni\Utils;

use Igni\Exception\UnexpectedValueException;

class Enum
{
    /** @var string */
    private $key;

    /** @var string */
    private $value;

    protected static $hash;

    public function __construct($value)
    {
        if (!static::validate($value)) {
            throw UnexpectedValueException::forInvalidValue(static::values(), $value);
        }
        $this->value = $value;
        $this->key = array_search($value, static::getHash(), true);
    }

    public static function values(): array
    {
        return array_values(static::getHash());
    }

    public static function keys(): array
    {
        return array_keys(static::getHash());
    }

    public static function has(string $key): bool
    {
        return isset(static::getHash()[$key]);
    }

    public static function validate($value): bool
    {
        return in_array($value, static::getHash(), $strict = true);
    }

    public static function __callStatic($name, $arguments): self
    {
        if (!self::has($name)) {
            throw UnexpectedValueException::forInvalidValue(static::keys(), $name);
        }

        return new static(self::getHash()[$name]);
    }

    public function key(): string
    {
        return $this->key;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Enum $enum): bool
    {
        return $this->value === $enum->value;
    }

    private static function getHash(): array
    {
        if (static::$hash) {
            return static::$hash;
        }
        $reflection = (new \ReflectionClass(get_called_class()));
        return static::$hash = $reflection->getConstants();
    }
}
