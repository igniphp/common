<?php declare(strict_types=1);

namespace Igni\Utils;

class ArrayUtil
{
    public static function set(&$array, $key, $value)
    {
        $key = explode('.', $key);
        $last = array_pop($key);
        $result = &$array;
        foreach ($key as $part) {
            if (!isset($result[$part]) || !is_array($result[$part])) {
                $result[$part] = [];
            }
            $result = &$result[$part];
        }
        $result[$last] = $value;

        return $array;
    }

    public static function remove(array &$array, $element): void
    {
        if (($key = array_search($element, $array, false)) !== false) {
            if (is_numeric($key)) {
                array_splice($array, $key, 1);
            } else {
                unset($array[$key]);
            }
        }
    }

    public static function has(array $array, $element): bool
    {
        return in_array($array, $element, false);
    }

    public static function get($array, $key)
    {
        return self::lookup($array, $key);
    }

    public static function exists($array, $key)
    {
        return self::lookup($array, $key) !== null;
    }

    private static function lookup($array, $key)
    {
        $result = $array;
        $key = explode('.', $key);
        foreach($key as $part) {
            if (!is_array($result) || !isset($result[$part])) {
                return null;
            }
            $result = $result[$part];
        }

        return $result;
    }

    public static function flatten($array): array
    {
        return static::flattenRecursive($array, '');
    }

    protected static function flattenRecursive(&$recursion, $prefix): array
    {
        $values = [];
        foreach ($recursion as $key => &$value) {
            if (is_array($value) && !empty($value)) {
                $values = array_merge($values, static::flattenRecursive($value, $prefix . $key . '.'));
            } else {
                $values[$prefix . $key] = $value;
            }
        }
        return $values;
    }

    public static function expand($array): array
    {
        $result = [];
        foreach ($array as $key => &$value) {
            $keys = explode('.', $key);
            $lastKey = array_pop($keys);
            $currentPointer = &$result;
            foreach ($keys as $currentKey) {
                if (!isset($currentPointer[$currentKey])) {
                    $currentPointer[$currentKey] = [];
                }
                $currentPointer = &$currentPointer[$currentKey];
            }
            $currentPointer[$lastKey] = $value;
        }
        return $result;
    }
}
