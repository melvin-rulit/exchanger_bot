<?php

namespace App\Exceptions\AppConfig;

use Exception;
use Throwable;
use App\Telegram\Traits\SendErrorsToTelegramTrait;

class ConfigNotFoundException extends Exception
{
    use SendErrorsToTelegramTrait;

    protected array $extraData;
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null, $extraData = null)
    {
        parent::__construct($message, $code, $previous);
        $this->extraData = $extraData;
    }

    public function report(): void
    {
        log_error($this->getMessage(), $this->extraData, 'env_config');
    }
}
