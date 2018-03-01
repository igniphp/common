<?php declare(strict_types=1);

namespace IgniTest\Unit\Util;

use Igni\Utils\TestCase;
use Igni\Utils\Token;

class TokenTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $token = new Token();

        self::assertInstanceOf(Token::class, $token);
    }

    public function testCanSignToken(): void
    {
        $token = new Token();
        $token->sign('secret');

        $string = (string) $token;

        $retrievedToken = Token::fromString($string);

        self::assertEquals($token->getData(), $retrievedToken->getData());
        self::assertTrue($retrievedToken->isValid('secret'));
        self::assertFalse($retrievedToken->isValid('invalid_secret'));
    }
}


