<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5162e4e2b9f1983a36d2fc1ca9e3f3cf
{
    public static $files = array (
        '46bf1fdf74afcb5cc9d45395436ee24c' => __DIR__ . '/..' . '/widmogrod/php-functional/src/Functional/functions.php',
        '8cdc1725b6247a1cc84c9911b0fcbebc' => __DIR__ . '/..' . '/widmogrod/php-functional/src/Functional/predicates.php',
        'e070b0f8d1547098ec6a4253c6affddd' => __DIR__ . '/..' . '/widmogrod/php-functional/src/Functional/strings.php',
        '4d86776776ff74ccf65cca67a5df7059' => __DIR__ . '/..' . '/widmogrod/php-functional/src/Monad/Either/functions.php',
        'be49bae08a3e58892601a353e9043baf' => __DIR__ . '/..' . '/widmogrod/php-functional/src/Monad/Maybe/functions.php',
        'a661e6a3d799b70d860e35339daaa92d' => __DIR__ . '/..' . '/widmogrod/php-functional/src/Monad/IO/functions.php',
        '09eaa491d0fd74f6a1379a1deb95e72b' => __DIR__ . '/..' . '/widmogrod/php-functional/src/Monad/IO/errors.php',
        '1ad0c1c1d771481281a7dc68e7438606' => __DIR__ . '/..' . '/widmogrod/php-functional/src/Monad/State/functions.php',
        '14befb123c1130ec9db69bcc4e1c46f4' => __DIR__ . '/..' . '/widmogrod/php-functional/src/Monad/Control/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Widmogrod\\' => 10,
        ),
        'B' => 
        array (
            'Bastelstube\\ParserCombinator\\' => 29,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Widmogrod\\' => 
        array (
            0 => __DIR__ . '/..' . '/widmogrod/php-functional/src',
        ),
        'Bastelstube\\ParserCombinator\\' => 
        array (
            0 => __DIR__ . '/..' . '/timwolla/parser-combinator/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5162e4e2b9f1983a36d2fc1ca9e3f3cf::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5162e4e2b9f1983a36d2fc1ca9e3f3cf::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}