<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5e6daed6745465add57dd83fa4c4f77e
{
    public static $files = array (
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Mbstring\\' => 26,
        ),
        'P' => 
        array (
            'PhpMyAdmin\\SqlParser\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'PhpMyAdmin\\SqlParser\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmyadmin/sql-parser/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5e6daed6745465add57dd83fa4c4f77e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5e6daed6745465add57dd83fa4c4f77e::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit5e6daed6745465add57dd83fa4c4f77e::$classMap;

        }, null, ClassLoader::class);
    }
}
