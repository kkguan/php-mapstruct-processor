<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Util;

class Str
{
    /**
     * The cache of camel-cased words.
     *
     * @var array
     */
    protected static $camelCache = [];

    /**
     * The cache of studly-cased words.
     *
     * @var array
     */
    protected static $studlyCache = [];

    protected static $keywords = [
        '__halt_compiler',
        'abstract',
        'and',
        'array',
        'as',
        'break',
        'callable',
        'case',
        'catch',
        'class',
        'clone',
        'const',
        'continue',
        'declare',
        'default',
        'die',
        'do',
        'echo',
        'else',
        'elseif',
        'empty',
        'enddeclare',
        'endfor',
        'endforeach',
        'endif',
        'endswitch',
        'endwhile',
        'eval',
        'exit',
        'extends',
        'final',
        'for',
        'foreach',
        'function',
        'global',
        'goto',
        'if',
        'implements',
        'include',
        'include_once',
        'instanceof',
        'insteadof',
        'interface',
        'isset',
        'list',
        'namespace',
        'new',
        'or',
        'print',
        'private',
        'protected',
        'public',
        'require',
        'require_once',
        'return',
        'static',
        'switch',
        'throw',
        'trait',
        'try',
        'unset',
        'use',
        'var',
        'while',
        'xor',
        '__CLASS__',
        '__DIR__',
        '__FILE__',
        '__FUNCTION__',
        '__LINE__',
        '__METHOD__',
        '__NAMESPACE__',
        '__TRAIT__',
    ];

    /**
     * 驼峰转下划线
     */
    public static function camelToUnderLine(string $str): string
    {
        $array = [];
        for ($i = 0; $i < strlen($str); ++$i) {
            if ($str[$i] == strtolower($str[$i])) {
                $array[] = $str[$i];
            } else {
                if ($i > 0) {
                    $array[] = '_';
                }
                $array[] = strtolower($str[$i]);
            }
        }

        return implode('', $array);
    }

    /**
     * @return mixed|string
     */
    public static function camel(string $value)
    {
        if (isset(static::$camelCache[$value])) {
            return static::$camelCache[$value];
        }

        return static::$camelCache[$value] = lcfirst(static::studly($value));
    }

    /**
     * Convert a value to studly caps case.
     */
    public static function studly(string $value, string $gap = ''): string
    {
        $key = $value;

        if (isset(static::$studlyCache[$key])) {
            return static::$studlyCache[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return static::$studlyCache[$key] = str_replace(' ', $gap, $value);
    }

    public static function joinAndCamelize(array $values): string
    {
        $isFirst = false;
        $str = '';
        foreach ($values as $value) {
            if ($isFirst) {
                $str = $value;
            } else {
                $isFirst = true;
                $str .= ucfirst(Str::camel($value));
            }
        }

        return $str;
    }

    /**
     * Returns a variable name which doesn't conflict with the given variable names existing in the same scope and the PHP keywords.
     *
     * @param string $name the name to get a safe version for existingVariableNames
     * @param array<string> $existingVariableNames the names of other variables existing in the same scope
     */
    public static function getSafeVariableName(string $name, array $existingVariableNames = []): string
    {
        $name = lcfirst(Str::sanitizeIdentifierName($name));
        $name = Str::joinAndCamelize(explode('\\.', $name));
        $conflictingNames = static::$keywords;
        foreach ($existingVariableNames as $existingVariableName) {
            $conflictingNames[] = $existingVariableName;
        }

        if (! in_array($name, $conflictingNames)) {
            return $name;
        }

        $c = 1;
        $separator = is_numeric(substr($name, strlen($name) - 1)) ? '_' : '';
        while (in_array($name . $separator . $c, $conflictingNames)) {
            ++$c;
        }

        return $name;
    }

    public static function sanitizeIdentifierName(string $identifier): string
    {
        // TODO: PHP内置命名未处理
        return $identifier;
    }

    public static function isUpper(int|string $codepoint): bool
    {
        $chr = ord($codepoint);
        return $chr >= 'A' && $chr <= 'Z';
    }

    public static function toLower(int|string $codepoint): int|string|null
    {
        if (strlen($codepoint) > 1) {
            return $codepoint;
        }

        if (is_numeric($codepoint)) {
            return $codepoint;
        }

        return strtolower($codepoint);
    }
}
