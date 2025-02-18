<?php


abstract class MoOAuthClientBasicEnum
{
    private static $constCacheArray = NULL;
    public static function getConstants()
    {
        if (!(self::$constCacheArray == NULL)) {
            goto sN2;
        }
        self::$constCacheArray = [];
        sN2:
        $by = get_called_class();
        if (array_key_exists($by, self::$constCacheArray)) {
            goto ez1;
        }
        $f1 = new ReflectionClass($by);
        self::$constCacheArray[$by] = $f1->getConstants();
        ez1:
        return self::$constCacheArray[$by];
    }
    public static function isValidName($O6, $o4 = false)
    {
        $XD = self::getConstants();
        if (!$o4) {
            goto NZi;
        }
        return array_key_exists($O6, $XD);
        NZi:
        $T3 = array_map("\163\x74\162\x74\157\x6c\x6f\x77\x65\162", array_keys($XD));
        return in_array(strtolower($O6), $T3);
    }
    public static function isValidValue($mB, $o4 = true)
    {
        $tv = array_values(self::getConstants());
        return in_array($mB, $tv, $o4);
    }
}
