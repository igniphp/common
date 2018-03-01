<?php declare(strict_types=1);

namespace Igni\IO\File;

use Igni\IO\File;

class Ini
{
    private const SECTION_START = '[';
    private const COMMENT_START_HASH = '#';
    private const COMMENT_START_SEMI = ';';
    private const SECTION_ATTRIBUTES_REGEX = '/((?:(?!\s|=).)*)\s*?=\s*?["\']?((?:(?<=")(?:(?<=\\\\)"|[^"])*|(?<=\')(?:(?<=\\\\)\'|[^\'])*)|(?:(?!"|\')(?:(?!\]|\s).)+))?/';
    private const SECTION_NAME_REGEX = '/\[(?:\s+)?([^\s\]]+)/';

    private $sections = [];
    /** @var File */
    private $file;
    private $parsed = false;
    private $prototypes = [];

    public function __construct(string $file, array $prototypes = [])
    {
        $this->setFile($file);
        $this->prototypes = $prototypes;
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function parse(): array
    {
        if ($this->parsed) {
            return $this->sections;
        }

        $lines = [];

        foreach ($this->file->readLines() as $line) {
            switch (mb_substr($line, 0, 1)) {
                case self::SECTION_START:
                    if (!empty($lines)) {
                        $this->parseSectionValues($lines);
                        $lines = [];
                    }
                    $this->parseSection($line);
                    break;
                case self::COMMENT_START_HASH:
                case self::COMMENT_START_SEMI:
                    continue;
                default:
                    $lines[] = $line;
            }
        }

        $this->file->close();

        if (!empty($lines)) {
            $this->parseSectionValues($lines);
        }

        $sections = $this->sections;
        $this->sections = [];

        foreach ($sections as $section) {
            $name = $section['@name'];

            // Check for prototype
            if (strstr($name, ':')) {
                $name = explode(':', $name);
                if (isset($this->prototypes[$name[1]])) {
                    $prototype = $this->prototypes[$name[1]];
                    if (is_array($prototype)) {
                        $section['@attributes'] = array_merge($this->prototypes[$name[1]], $section['@attributes']);
                    } elseif (is_callable($prototype)) {
                        $section['@attributes'] = $prototype($section);
                    }
                }
                $name = $name[0];
            }

            if (!isset($this->sections[$name])) {
                $this->sections[$name] = [];
            }

            if (isset($section['@attributes']['name'])) {
                $key = $section['@attributes']['name'];
                $this->sections[$name][$key] = array_merge($section['@values'], $section['@attributes']);
            } else {
                $this->sections[$name] = array_merge($section['@values'], $section['@attributes']);
            }
        }
        $this->parsed = true;

        return $this->sections;
    }

    private function parseSection(string $header): void
    {
        $section = [
            '@attributes' => [],
            '@name' => '',
            '@values' => [],
        ];
        preg_match(self::SECTION_NAME_REGEX, $header, $matches);
        $section['@name'] = $matches[1];
        preg_match_all(self::SECTION_ATTRIBUTES_REGEX, $header, $matches, PREG_SET_ORDER, 0);
        $attributes = $matches;
        foreach ($attributes as $attribute) {
            $section['@attributes'][$attribute[1]] = $attribute[2];
        }
        $this->sections[] = $section;
    }

    private function parseSectionValues(array $sectionValues): void
    {
        if (empty($this->sections)) {
            $this->sections[] = [
                '@attributes' => [],
                '@name' => 'global',
                '@values' => parse_ini_string(implode(PHP_EOL, $sectionValues))
            ];

            return;
        }

        $this->sections[count($this->sections) - 1]['@values'] = parse_ini_string(implode(PHP_EOL, $sectionValues));
    }

    private function setFile(string $file): void
    {
        $this->file = new File($file);
    }
}
