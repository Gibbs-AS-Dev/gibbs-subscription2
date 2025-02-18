<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd144ab352c2940bc942ff15af4b3936f
{
    public static $prefixLengthsPsr4 = array (
        'R' => 
        array (
            'RRule\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'RRule\\' => 
        array (
            0 => __DIR__ . '/..' . '/rlanvin/php-rrule/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd144ab352c2940bc942ff15af4b3936f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd144ab352c2940bc942ff15af4b3936f::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitd144ab352c2940bc942ff15af4b3936f::$classMap;

        }, null, ClassLoader::class);
    }
}
