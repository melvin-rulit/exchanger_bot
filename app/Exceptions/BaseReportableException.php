<?php
namespace App\Exceptions;

use Exception;
use Throwable;
use App\Telegram\Traits\SendErrorsToTelegramTrait;
use App\Exceptions\Helpers\InvalidStringValueException;

abstract class BaseReportableException extends Exception
{
    use SendErrorsToTelegramTrait;

    protected array $extraData = [];

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null, array $extraData = [])
    {
        parent::__construct($message, $code, $previous);
        $this->extraData = $extraData;
    }

    /**
     * @throws InvalidStringValueException
     */
    public function report(): void
    {
        log_error($this->getMessage(), $this->extraData, 'database');

        $this->sendToTelegram($this->getMessage(), $this->extraData);
    }
}

