<?php

namespace App\Exceptions\Country;

use App\Telegram\Traits\SendErrorsToTelegramTrait;
use Exception;
use Throwable;
use App\Services\ErrorNotifierService;

class CountryNotFoundException extends Exception
{
    use SendErrorsToTelegramTrait;

    protected array $extraData;
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null, $extraData = null, protected ErrorNotifierService $errorNotifierService)
    {
        parent::__construct($message, $code, $previous);
        $this->extraData = $extraData;
    }

    public function report(): void
    {
        log_error($this->getMessage(), $this->extraData, 'database');

        $this->sendToTelegram($this->getMessage(), $this->extraData, 'database');
    }
}
