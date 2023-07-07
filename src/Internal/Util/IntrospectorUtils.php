<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Util;

class IntrospectorUtils
{
    private function __construct()
    {
    }

    /**
     * Utility method to take a string and convert it to normal Java variable
     * name capitalization.  This normally means converting the first
     * character from upper case to lower case, but in the (unusual) special
     * case when there is more than one character and both the first and
     * second characters are upper case, we leave it alone.
     * <p>
     * Thus "FooBah" becomes "fooBah" and "X" becomes "x", but "URL" stays
     * as "URL".
     *
     * @param string name the string to be decapitalized
     *
     * @return string The decapitalized version of the string
     */
    public static function decapitalize(string $name): string
    {
        if (strlen($name) == 0) {
            return $name;
        }

        if (strlen($name) > 1 && Str::isUpper($name[0]) && Str::isUpper($name[1])) {
            return $name;
        }

        $name[0] = Str::toLower($name[0]);
        return $name;
    }
}
