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
    protected string $chanel = '';

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null, array $extraData = [], string $chanel = null)
    {
        parent::__construct($message, $code, $previous);
        $this->extraData = $extraData;
        $this->chanel = $chanel;
    }

    /**
     * @throws InvalidStringValueException
     */
    public function report(): void
    {
        log_error($this->getMessage(), $this->extraData, $this->chanel);

        $this->sendToTelegram($this->getMessage(), $this->extraData);
    }
}

