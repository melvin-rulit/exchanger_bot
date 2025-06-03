<?php

namespace App\DTO;

class CallbackReturnMenu
{
    public function __construct(public string $action, public ?int $clientBotId = null, public ?string $clientStatus = null) {}

    public static function fromTelegram(array $data): self
    {
        if ($data['callbackReturnMenuKey'] === __('buttons.back_menu')) {
            return new self(__('buttons.back_menu'), $data['clientBotId'], $data['clientStatus']);
        }

        return new self('unknown');
    }
}
