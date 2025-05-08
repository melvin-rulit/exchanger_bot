<?php

return [
    'telegram_bot' => [
        'api_url' => 'https://api.telegram.org/bot',
        'api_file_url' => 'https://api.telegram.org/file/bot',
        'token' => env('TELEGRAM_BOT_TOKEN'),
    ],
    'error_notifier_bot' => [
        'token' => env('ERROR_NOTIFIER_BOT_TOKEN'),
        'chat_id' => env('ERROR_NOTIFIER_CHAT_ID'),
    ],
];

