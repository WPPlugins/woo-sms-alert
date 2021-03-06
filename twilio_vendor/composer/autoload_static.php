<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7f91ed7f04cd2290126d182a57a7165e
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Twilio\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Twilio\\' => 
        array (
            0 => __DIR__ . '/..' . '/twilio/sdk/Twilio',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7f91ed7f04cd2290126d182a57a7165e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7f91ed7f04cd2290126d182a57a7165e::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
