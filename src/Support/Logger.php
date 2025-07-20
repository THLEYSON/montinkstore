<?php

namespace App\Support;

class Logger
{
    private static string $logFile = __DIR__ . '/../../storage/logs/app.log';

    public static function error(string $message): void
    {
        self::writeLog('ERROR', $message);
    }

    public static function warning(string $message): void
    {
        self::writeLog('WARNING', $message);
    }

    public static function info(string $message): void
    {
        self::writeLog('INFO', $message);
    }

    private static function writeLog(string $level, string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $entry = "[$timestamp] [$level] $message" . PHP_EOL;
        file_put_contents(self::$logFile, $entry, FILE_APPEND);
    }
}
