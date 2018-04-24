# ![Igni logo](../logo/full.svg)

## Igni\Util\StringUtils

Provides various string utility functions (multi-byte string safe).

### API

#### `contains(string $string, string $needle, bool $caseSensitive = true): bool`
Checks if string contains passed needle.

#### `substring(string $string, int $start = 0, int $length = null): string`
Checks if string contains passed needle.

#### `length(string $string): int`
Returns length of the string

#### `lowerCaseFirst(string $string): string`
Changes first character in the string sequence into lowercase character.

#### `toLowerCase(string $string): string`
Changes entire string sequence into lower case representation of that string.

#### `upperCaseFirst(string $string): string`
Changes first character in the string sequence into upper character.

#### `toUpperCase(string $string): string`
Changes entire string sequence into upper case representation of that string.

#### `at(string $string, int $index): string`
Returns character at given index position from the passed string.

#### `reverse(string $string): string`
Returns reversed version of the string sequence

#### `trim(string $string, string $char = ' '): string`
Returns both side trimmed version of the string sequence. String is trimmed by using `$char`.

#### `trimLeft(string $string, string $char = ' '): string`
Returns left-trimmed version of the string sequence. String is trimmed by using `$char`.

#### `trimRight(string $string, string $char = ' '): string`
Returns right-trimmed version of the string sequence. String is trimmed by using `$char`.

#### `slugify(string $string, string $replacementChar = '-'): string`
Returns url-safe version of the passed string, all special characters are replaced by $replacementChar, all 
diacritic characters are replaced by ANSI substitutes. 
