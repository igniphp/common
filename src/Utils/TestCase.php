<?php declare(strict_types=1);

namespace Igni\Utils;

use Mockery;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use ReflectionClass;

/**
 * Helper class for running PHPUnit tests
 */
abstract class TestCase extends PHPUnitTestCase
{
    public static function makeAccessibleMethod($object, $method): \ReflectionMethod
    {
        if (is_object($object)) {
            $reflection = new ReflectionClass(get_class($object));
        } else {
            $reflection = new ReflectionClass($object);
        }
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);

        return $method;
    }

    public static function makeAccessibleProperty($object, $property): \ReflectionProperty
    {
        if (is_object($object)) {
            $reflection = new ReflectionClass(get_class($object));
        } else {
            $reflection = new ReflectionClass($object);
        }
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);

        return $property;
    }

    public static function invokeMethod($object, $method, array $parameters)
    {
        $method = self::makeAccessibleMethod($object, $method);
        return $method->invokeArgs($object, $parameters);
    }

    public static function readProperty($object, string $property)
    {
        return self::makeAccessibleProperty($object, $property)->getValue($object);
    }

    public static function writeProperty($object, string $property, $value): void
    {
        $property = self::makeAccessibleProperty($object, $property);
        if (is_object($object)) {
            $property->setValue($object, $value);
        } else {
            $property->setValue($value);
        }
    }

    /**
     * Static shortcut to \Mockery\Container::mock().
     *
     * @return Mockery\MockInterface
     */
    public static function mock() : Mockery\MockInterface
    {
        $args = func_get_args();
        return call_user_func_array([Mockery::getContainer(), 'mock'], $args);
    }
}
