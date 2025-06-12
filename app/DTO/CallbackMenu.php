<?php

namespace App\DTO;

use Illuminate\Support\Facades\Log;

class CallbackMenu
{
    public function __construct(public string $action, public ?int $clientBotId = null) {}

    public static function fromTelegram(array $data): self
    {
        if ($data['callbackMenuKey'] === '💳 ' . __('buttons.get_requisite')) {
            return new self('💳 ' . __('buttons.get_requisite'), $data['clientBotId']);
        }
        if ($data['callbackMenuKey'] === '👩‍💻 ' . __('buttons.consultation')) {
            return new self('👩‍💻 ' . __('buttons.consultation'), $data['clientBotId']);
        }
        if ($data['callbackMenuKey'] === '🌐 ' . __('buttons.change_language')) {
            return new self('🌐 ' . __('buttons.change_language'), $data['clientBotId']);
        }

        return new self('unknown');
    }
}
