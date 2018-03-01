<?php declare(strict_types=1);

namespace Igni\Utils;

use Igni\Exception\RuntimeException;
use DateTimeInterface;
use DateTimeImmutable;

/**
 * Generates token that can be used for authentication.
 * Token can keep serialized if required.
 * It is uses sha256 for generating secure signature for singing the token.
 */
class Token
{
    /**
     * @var array User defined data
     */
    private $data;

    /**
     * @var DateTimeImmutable
     */
    private $createdOn;

    /**
     * @var DateTimeImmutable
     */
    private $expiresOn;

    private $secret = '';

    private $checksum = '';

    /**
     * @var string Serialized token
     */
    private $value;

    public function __construct(array $data = [], DateTimeInterface $validUntil = null)
    {
        if (empty($data)) {
            $data = ['_' => Uuid::generateShort()];
        }

        $this->data = $data;

        $this->createdOn = new DateTimeImmutable();
        $this->expiresOn = $validUntil ?? (new DateTimeImmutable())->modify('+1 year');
    }

    public function sign(string $secret): void
    {
        $this->secret = $secret;
    }

    public function getExpirationTime(): DateTimeInterface
    {
        return $this->expiresOn;
    }

    public function hasExpired(): bool
    {
        return $this->expiresOn < new DateTimeImmutable();
    }

    public function isValid(string $secret = ''): bool
    {
        $checksum = self::createChecksum(self::packData($this->createdOn, $this->expiresOn, $this->data), $secret);
        return $checksum === $this->checksum;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function __toString(): string
    {
        if ($this->value) {
            return $this->value;
        }

        if (!$this->secret) {
            throw new RuntimeException("Cannot get string representation of unsigned token.");
        }

        $packedData = self::packData($this->createdOn, $this->expiresOn, $this->data);
        $checksum = self::createChecksum($packedData, $this->secret);

        return $this->value = $packedData . $checksum;
    }

    public static function fromString(string $token): Token
    {
        $instance = new Token();
        $instance->checksum = substr($token, -20);
        $data = self::unpackData(substr($token, 0, -20));
        $instance->value = $token;
        $instance->expiresOn = $data['expires_on'];
        $instance->createdOn = $data['created_on'];
        $instance->data = $data['user_data'];

        return $instance;
    }

    private static function unpackData(string $serialized): array
    {
        $byteString = Base58::decode($serialized);
        $data = unpack('Lcreated_on/Lexpires_on/Z*user_data', $byteString);
        parse_str($data['user_data'], $data['user_data']);
        $data['created_on'] = new DateTimeImmutable('@' . $data['created_on']);
        $data['expires_on'] = new DateTimeImmutable('@' . $data['expires_on']);

        return $data;
    }

    private function packData(DateTimeInterface $created, DateTimeInterface $expires, array $data): string
    {
        $byteArray = pack('LLZ*', $created->getTimestamp(), $expires->getTimestamp(), http_build_query($data));

        return Base58::encode($byteArray);
    }

    private static function createChecksum(string $packedData, string $secret): string
    {
        return substr(hash('sha256', $secret . $packedData), -20);
    }
}
