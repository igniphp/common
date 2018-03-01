<?php declare(strict_types=1);

namespace Igni\Exception;

/**
 * Exception that represents error in the program logic.
 * This kind of exception should lead directly to a fix in your code.
 */
class InvalidArgumentException extends \InvalidArgumentException implements Exception
{
}
