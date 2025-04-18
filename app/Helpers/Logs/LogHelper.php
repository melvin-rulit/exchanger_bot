<?php

namespace App\Helpers\Logs;

use Illuminate\Support\Facades\Log;

class LogHelper
{
    public static function info(string $message, array $context = [], string $channel = null): void
    {
        self::writeLog('info', $message, $context, $channel);
    }

    public static function error(string $message, array $context = [], string $channel = null): void
    {
        self::writeLog('error', $message, $context, $channel);
    }

    public static function debug(string $message, array $context = [], string $channel = null): void
    {
        self::writeLog('debug', $message, $context, $channel);
    }

    protected static function writeLog(string $level, string $message, array $context, ?string $channel): void
    {
        $logger = $channel ? Log::channel($channel) : Log::channel(); // default if no channel
        $logger->{$level}(self::format($message), $context);
    }

    protected static function format(string $message): string
    {
        return '[LOG] ' . $message;
    }
}
