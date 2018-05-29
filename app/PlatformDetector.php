<?php

namespace App;

class PlatformDetector
{
    public static $WINDOWS = 'windows';
    public static $LINUX = 'linux';
    public static $MACOS = 'mac';
    public static function detect($platform)
    {
        switch ($platform) {
            case 'darwin':
            case 'mac':
            case 'macos':
                return self::$MACOS;
            case 'win32':
            case 'windows':
                return self::$WINDOWS;
            default:
                return self::$LINUX;
        }
    }
}
