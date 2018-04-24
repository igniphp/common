<?php declare(strict_types=1);

namespace Igni\Utils;

final class Uuid
{
    use StaticClassTrait;

    public static function generate(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);// set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);// set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function generateShort(): string
    {
        return self::toShort(self::generate());
    }

    public static function toShort($uuid): string
    {
        return Base58::encode(self::toBinary($uuid));
    }

    public static function fromShort(string $uuid): string
    {
        $uuid = Base58::decode($uuid);
        $uuid = implode('', unpack("h*", $uuid));

        return substr($uuid, 0, 8) . '-' .
            substr($uuid, 8, 4) . '-' .
            substr($uuid, 12, 4) . '-' .
            substr($uuid, 16, 4) . '-' .
            substr($uuid, 20);
    }

    public static function validate(string $uuid): bool
    {
        $uuidPattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';

        if (!preg_match($uuidPattern, $uuid)) {
            return false;
        }

        return true;
    }

    private static function toBinary($uuid): string
    {
        return pack("h*", str_replace('-', '', $uuid));
    }
}
