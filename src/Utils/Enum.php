<?php declare(strict_types=1);

namespace Igni\Utils;

use UnexpectedValueException;

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
            throw new UnexpectedValueException("Invalid value ${value}, expected one of: " . static::values());
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
            throw new UnexpectedValueException("Invalid value ${name}, expected one of: " . static::keys());
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
        $reflection = ReflectionApi::reflectClass(get_called_class());

        return static::$hash = $reflection->getConstants();
    }
}
