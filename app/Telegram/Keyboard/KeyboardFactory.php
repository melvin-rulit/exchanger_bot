<?php

namespace App\Telegram\Keyboard;

use App\Enums\TelegramCallbackAction;

class KeyboardFactory
{
    public static function startKeyboard(): array
    {
        return [
            'keyboard' => [
                //[
                //    ['text' => __('buttons.transfer')],
                //],
                [
                    ['text' => '💳 ' . __('buttons.get_requisite')],
                ],
                [
                    ['text' => '👩‍💻 ' . __('buttons.consultation')],
                ],
                [
                    ['text' => '🌐 ' . __('buttons.change_language')],
                ],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ];
    }

    public static function languageKeyboard(): array
    {
        return [

            'inline_keyboard' => [
                [
                    ['text' => '🇷🇺 ' . __('buttons.RU'), 'callback_data' => 'language_ru'],
                    ['text' => '🇬🇧 ' . __('buttons.EN'), 'callback_data' => 'language_en'],
                ],

                [
                    ['text' => __('buttons.to_main'), 'callback_data' => TelegramCallbackAction::ToMain->value]
                ],
            ]
        ];
    }

    public static function toMain(): array
    {
        return [
            'keyboard' => [
                [
                    ['text' => __('buttons.to_main')],
                ],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ];
    }

    public static function toBack($callbackData): array
    {
        return [
            [
                'text' => '◀️ '. __('buttons.back'), 'callback_data' => $callbackData
            ]
        ];
    }
    public static function toBackMenu(): array
    {
        return [
            'keyboard' => [
                [
                    ['text' => __('buttons.back_menu')],
                ],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ];
    }
    public static function toCancel(bool $inlineType = false): array
    {
        if ($inlineType) {
            return [
                [
                    'text' => '❌ '. __('buttons.cancel'), 'callback_data' => TelegramCallbackAction::Cancel->value
                ]
            ];
        }
            return [
                'keyboard' => [
                    [
                        ['text' => __('buttons.cancel')],
                    ],
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true,
            ];
    }
    public static function toConsultation($callbackData): array
    {
        return [
            [
                'text' => '❓ '. __('buttons.consultation_question'), 'callback_data' => $callbackData
            ]
        ];
    }
}
