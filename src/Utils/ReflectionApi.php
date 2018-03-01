<?php declare(strict_types=1);

namespace Igni\Utils;

use Closure;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;

final class ReflectionApi
{
    private static $reflections;

    /**
     * @param string $className
     * @param array $arguments
     * @return object
     */
    public static function createInstance(string $className, array $arguments = null)
    {
        $reflection = self::reflectClass($className);

        if (null === $arguments) {
            return $reflection->newInstanceWithoutConstructor();
        }

        return $reflection->newInstanceArgs($arguments);
    }

    public static function writeProperty($instance, string $name, $value): void
    {
        $reflection = self::reflectClass(get_class($instance));

        $property = $reflection->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($instance, $value);
    }

    public static function reflectClass(string $className): ReflectionClass
    {
        if (isset(self::$reflections[$className])) {
            return self::$reflections[$className];
        }

        return self::$reflections[$className] = new ReflectionClass($className);
    }

    public static function reflectFunction($function): ReflectionFunction
    {
        if (!is_string($function)) {
            return new ReflectionFunction($function);
        }

        if (isset(self::$reflections[$function])) {
            return self::$reflections[$function];
        }
        return self::$reflections[$function] = new ReflectionFunction($function);
    }

    public static function reflectMethod(string $class, string $method): ReflectionMethod
    {
        $class = self::reflectClass($class);
        return $class->getMethod($method);
    }
}
