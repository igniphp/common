<?php declare(strict_types=1);

namespace Igni\Utils;

use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;

final class ReflectionApi
{
    private static $reflections;

    /**
     * Creates instance of the given class.
     *
     * @param string $className
     * @param array $arguments
     * @return object
     * @throws \ReflectionException
     */
    public static function createInstance(string $className, array $arguments = null)
    {
        $reflection = self::reflectClass($className);

        if (null === $arguments) {
            return $reflection->newInstanceWithoutConstructor();
        }

        return $reflection->newInstanceArgs($arguments);
    }

    /**
     * Checks if given $class (class or object) is instance of given $interfaceOrClass.
     *
     * @param $class
     * @param string $interfaceOrClass
     * @return bool
     */
    public static function isSubclassOf($class, string $interfaceOrClass): bool
    {
        $isSubclass = is_subclass_of($class, $interfaceOrClass, true);

        if (!$isSubclass) {
            return in_array($interfaceOrClass, class_implements($class));
        }

        return $isSubclass;
    }

    /**
     * Overrides object's property value.
     *
     * @param $instance
     * @param string $name
     * @param $value
     * @throws \ReflectionException
     */
    public static function writeProperty($instance, string $name, $value): void
    {
        $reflection = self::reflectClass(get_class($instance));

        $property = $reflection->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($instance, $value);
    }

    /**
     * Creates and caches in memory reflection of the given class.
     *
     * @param string $className
     * @return ReflectionClass
     * @throws \ReflectionException
     */
    public static function reflectClass(string $className): ReflectionClass
    {
        if (isset(self::$reflections[$className])) {
            return self::$reflections[$className];
        }

        return self::$reflections[$className] = new ReflectionClass($className);
    }

    /**
     * Creates and caches in memory reflection of the given function.
     *
     * @param $function
     * @return ReflectionFunction
     * @throws \ReflectionException
     */
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

    /**
     * Creates and caches in memory reflection of the given method.
     *
     * @param string $class
     * @param string $method
     * @return ReflectionMethod
     * @throws \ReflectionException
     */
    public static function reflectMethod(string $class, string $method): ReflectionMethod
    {
        $class = self::reflectClass($class);

        return $class->getMethod($method);
    }
}
