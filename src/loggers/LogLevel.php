<?php

namespace App\loggers;

use ReflectionClass;
use ReflectionClassConstant;

class LogLevel
{
    const string EMERGENCY = 'emergency';
    const string ALERT = 'alert';
    const string CRITICAL = 'critical';
    const string ERROR = 'error';
    const string WARNING = 'warning';
    const string NOTICE = 'notice';
    const string INFO = 'info';
    const string DEBUG = 'debug';

    public static function isLogLevel(string $level): bool
    {
        $reflection = new ReflectionClass(self::class);
        $constants = $reflection->getConstants(ReflectionClassConstant::IS_PUBLIC);
        return in_array($level, $constants);
    }
}
