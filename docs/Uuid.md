#![Igni logo](../logo/full.svg)

## Igni\Util\Uuid

RFC-compliant Universally Unique Identifiers v5 generator.

### API

#### `generate(): string`
Generates uuid.

#### `generateShort(): string`
Generates shorter representation of uuid (base58 encoded).

#### `toShort(string $string): string`
Shorts uuid by packing it into base58 representation.

#### `fromShort(string $string): string`
Returns full uuid as a string from base58 representation.

#### `validate(string $string): bool`
Validates if passed string is valid uuid number.
