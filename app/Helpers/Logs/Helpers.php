<?php

use App\Helpers\Logs\LogHelper;

function log_info(string $message, array $context = [], string $channel = null): void
{
  LogHelper::info($message, $context, $channel);
}

function log_error(string $message, array $context = [], string $channel = null): void
{
    LogHelper::error($message, $context, $channel);
}

function log_debug(string $message, array $context = [], string $channel = null): void
{
    LogHelper::debug($message, $context, $channel);
}
