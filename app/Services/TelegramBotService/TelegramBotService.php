<?php

namespace App\Services\TelegramBotService;

use App\Models\Country;
use App\Models\Currency;
use App\Services\ChatService\ChatService;
use App\Services\ClientService\ClientsService;
use App\Services\CredentialService\CredentialService;
use App\Services\OrderService\OrderService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class TelegramBotService implements TelegramBotServiceInterface
{
    protected string $url;
    protected ChatService $chatService;
    protected ClientsService $clientsService;
    protected OrderService $orderService;
    protected CredentialService $credentialService;
    protected bool $isInMainMenu = false;

    public function __construct(ChatService $chatService, ClientsService $clientsService, OrderService $orderService, CredentialService $credentialService)
    {
        $this->url = config('telegram.telegram_bot.api_url') . config('telegram.telegram_bot.token');
        $this->chatService = $chatService;
        $this->clientsService = $clientsService;
        $this->orderService = $orderService;
        $this->credentialService = $credentialService;
    }

    public function getWebchook($WebchookData): void
    {
        if (isset($WebchookData['callback_query'])) {
            $callbackData = $WebchookData['callback_query']['data'];
            $chatId = $WebchookData['callback_query']['message']['chat']['id'];
            $clientId = $WebchookData['callback_query']['from']['id'];
            $messageId = $WebchookData['callback_query']['message']['message_id'];

            $this->setClientLanguage($this->clientsService->getClientLanguage($clientId));

            switch ($callbackData) {
                case 'language_ru':
                    $this->changeLanguage($chatId, $clientId, 'ru', $messageId);
                    break;
                case 'language_en':
                    $this->changeLanguage($chatId, $clientId, 'en', $messageId);
                    break;
                case 'to_main':
                    $this->sendStartMessageWithButtons($chatId, $messageId, $clientId);
                    break;

                case (strpos($callbackData, 'country_') === 0):
                    $countryCode = str_replace('country_', '', $callbackData);

                    if ($countryCode) {
                        $this->handleCountrySelection($chatId, $clientId, $countryCode, $messageId);
                    }
                    break;

                case (strpos($callbackData, 'bank_') === 0):
                    $bank_id = str_replace('bank_', '', $callbackData);

                    if ($bank_id) {
                        $this->handleBankSelection($chatId, $clientId, $bank_id, $messageId);
                    }
                    break;

                case (strpos($callbackData, 'currency') === 0):
                    $currency_id = str_replace('currency', '', $callbackData);

                    if ($currency_id) {
                        $this->handleCurrencySelection($chatId, $clientId, $currency_id, $messageId);
                    }
                    break;

                default:
                    break;

            }

            return;
        }

        if (isset($WebchookData['message']['photo'])) {
            $clientId = $WebchookData['message']['from']['id'];
            $chatId = $WebchookData['message']['chat']['id'];
            $photoCount = count($WebchookData['message']['photo']);
            $this->getAndSavePhoto($WebchookData['message']['photo'][$photoCount - 1], $clientId, $chatId);

        } elseif (isset($WebchookData['message']['text'])) {
            $text = $WebchookData['message']['text'];
            $chatId = $WebchookData['message']['chat']['id'];
            $clientId = $WebchookData['message']['from']['id'];
            $messageId = $WebchookData['message']['message_id'];
            $this->clientsService->checkIfClientExit($WebchookData);
            $this->setClientLanguage($this->clientsService->getClientLanguage($clientId));

//            if ($messageId) {
////            $saveMessages = $this->saveMessagesForDeleted($messageId);
//            }

            if ($this->clientsService->isUserInAmountInput($clientId) && !$this->checkMainMenu($text, $clientId, $chatId, $messageId)) {
                if ($this->isValidAmount($text, $clientId)) {
                    $amount = $text;
                    $this->checkProcessAmount($chatId, $clientId, $amount);
                } else {
                    $this->sendInvalidAmountMessage($chatId, $messageId);
                }
            } elseif ($this->clientsService->isUserInACountryInput($clientId) && $text !== __('buttons.change_language') && $text === '' && !$this->checkMainMenu($text, $clientId, $chatId, $messageId) && !$this->clientsService->isUserInAmountInput($clientId)) {


            } elseif ($text === __('buttons.change_language') && !$this->clientsService->isUserInACountryInput($clientId) && !$this->checkMainMenu($text, $clientId, $chatId, $messageId) && !$this->clientsService->isUserInAmountInput($clientId)) {
                $this->sendMessageChangeLanguage($chatId, $clientId, $messageId);
            } elseif (!$this->checkMainMenu($text, $clientId, $chatId, $messageId) && $this->clientsService->isClientSendScreenshot($clientId)) {
//                Log::info('checkMainMenu вернул: ' . json_encode($this->checkMainMenu($text, $clientId, $chatId, $messageId)));
                $save_order_id = Redis::get('save_order_id_for' . $clientId);

                $this->chatService->prepareSaveMessage($text, $chatId, $save_order_id);
//                if ($save_order_id && $text) {
//
//                }elseif($text) {
//                    $this->chatService->prepareSaveMessage($text, $chatId);
//                }

            } elseif (!$this->checkMainMenu($text, $clientId, $chatId, $messageId) && $this->clientsService->isClientConsultationInput($clientId)){
                if (isset($WebchookData['message']['photo'])) {
//                    $clientId = $WebchookData['message']['from']['id'];
//                    $chatId = $WebchookData['message']['chat']['id'];
//                    $photoCount = count($WebchookData['message']['photo']);
//                    $this->getAndSavePhoto($WebchookData['message']['photo'][$photoCount - 1], $clientId, $chatId);else{
//                    $this->chatService->prepareSaveMessage($text, $chatId);
                        Log::info($WebchookData['message']['photo']['file_id']);
                }

            }else {
                if (isset($text)) {

                    if ($this->checkStartMessage($text, $clientId) || $this->checkMainMenu($text, $clientId, $chatId, $messageId)) {
                        $this->sendStartMessageWithButtons($chatId, $clientId, $messageId);
                    } else {
                        switch ($text) {
                            case __('buttons.transfer'):
                                break;
                            case __('buttons.get_requisite'):

                                $this->sendAccountDetails($chatId, $clientId, $messageId);
                                break;
                            case '👩‍💻 ' . __('buttons.consultation'):
                                $this->sendConsultationMessage($chatId, $clientId);
                                break;
                            case __('buttons.change_language'):
                                $this->sendMessageChangeLanguage($chatId, $clientId, $messageId);
                                break;
                            default:
                                $this->sendStartMessageWithButtons($chatId, $messageId, $clientId);
                                break;
                        }
                    }
                }
            }
        }
    }
    public function changeLanguage($chatId, $clientId, $language, $messageId): void
    {
        $isSetLanguage = $this->clientsService->setClientChangeLanguageInput($clientId, $language, false);
        if ($isSetLanguage) {
            $this->clientsService->setClientMainInput($clientId, __('buttons.to_main'));

            $language = $this->clientsService->getClientLanguage($clientId);
            App::setLocale($language);
            $this->sendStartMessageWithButtons($chatId, $messageId, $clientId);
        }
    }
    public function setClientLanguage($language): void
    {
        //Log::alert(app()->getLocale());
        App::setLocale($language);
    }
    public function checkStartMessage($text, $clientId)
    {
        $this->clientsService->setClientMainInput($clientId, __('buttons.to_main'));
        return $text === '/start';
    }
    public function checkMainMenu($text, $clientId, $chatId, $messageId): bool
    {
        if ($text === __('buttons.to_main') || $text === __('buttons.cancel')) {
//            $this->clientsService->setClientMainInput($clientId, __('buttons.to_main'));
//            $this->sendStartMessageWithButtons($chatId, $messageId, $clientId);
            return true;
        }
        return false;
    }
    public function sendMessageChangeLanguage($chatId, $clientId, $messageId)
    {
        $this->clientsService->setClientChangeLanguageInput($clientId, null, true);
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🇷🇺 ' . __('buttons.RU'), 'callback_data' => 'language_ru'],
                    ['text' => '🇬🇧 ' . __('buttons.EN'), 'callback_data' => 'language_en'],
                ],
                [
//                    ['text' => __('buttons.to_main'), 'callback_data' => 'to_main'],
                ],
            ]
        ];
        $this->sendMessageWithButton($chatId, $keyboard, __('messages.get_language'));

    }

//    public function checkChangeLanguageMessage($text, $clientId): bool
//    {
//        if ($text === __('buttons.change_language')) {
//            $this->clientsService->setClientChangeLanguageInput($clientId, __('buttons.change_language'), false);
//            return true;
//        }
//        return false;
//    }

    public function sendStartMessageWithButtons($chatId, $messageId, $clientId): void
    {
        $keyboard = [
            'keyboard' => [
                [
                    //['text' => __('buttons.transfer')],
                ],
                [
                    ['text' => __('buttons.get_requisite')]
                ],
                [
                    ['text' => '👩‍💻 ' . __('buttons.consultation')]
                ],
                [
                    ['text' => __('buttons.change_language')]
                ],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ];
        $this->clientsService->setClientMainInput($clientId, __('buttons.to_main'));
        $this->deleteMessage($chatId, $messageId);

        if(!$this->isInMainMenu){
            $this->sendMessageWithButton($chatId, $keyboard, __('messages.greeting'));
        }
    }

    public function sendAccountDetails($chatId, $clientId, $messageId): void
    {
        $language = $this->clientsService->getClientLanguage($clientId);
        $this->clientsService->setUserCountryInput($clientId);
        $countries = Country::where('is_used', true)->get();

        $keyboard = [
            'inline_keyboard' => [],
        ];

        $countryNameField = ($language === 'ru') ? 'name_ru' : 'name_en';
        $row = [];

        foreach ($countries as $index => $country) {
            $row[] = [
                'text' => ($country->flag ?? '') . ' ' . $country->$countryNameField,
                'callback_data' => 'country_' . $country->code
            ];

            if (count($row) == 2) {
                $keyboard['inline_keyboard'][] = $row;
                $row = [];
            }
        }

        if (count($row) > 0) {
            $keyboard['inline_keyboard'][] = $row;
        }

        $this->sendMessageWithButton($chatId, $keyboard, __('messages.get_country'), $messageId);
    }
    public function handleCountrySelection($chatId, $clientId, $countryCode, $messageIdToDelete): void
    {
        $country = Country::with('banks')->where('code', $countryCode)->first();
        Redis::set('select_country_for_' . $clientId, $country->id);

        $keyboard = [
            'inline_keyboard' => [],
        ];

        $row = [];

        foreach ($country->banks as $index => $bank) {
            $row[] = [
                'text' => $bank->name,
                'callback_data' => 'bank_' . $bank->id
            ];
            if (count($row) == 2) {
                $keyboard['inline_keyboard'][] = $row;
                $row = [];
            }
        }
        if (count($row) > 0) {
            $keyboard['inline_keyboard'][] = $row;
        }

        $this->clientsService->setClientBankInput($clientId);
        $this->sendMessageWithButton($chatId, $keyboard, __('messages.get_country_success'), $messageIdToDelete);
    }

    public function handleBankSelection($chatId, $clientId, $bank_id, $messageIdToDelete): void
    {
        $select_country_id = Redis::get('select_country_for_' . $clientId);
        $country_currency = Currency::where('country_id', $select_country_id)->get();

        $keyboard = [
            'inline_keyboard' => [],
        ];

        if ($country_currency) {
            $row = [];

            foreach ($country_currency as $index => $currency) {
                $row[] = [
                    'text' => $currency->name,
                    'callback_data' => 'currency' . $currency->id
                ];
                if (count($row) == 3) {
                    $keyboard['inline_keyboard'][] = $row;
                    $row = [];
                }
            }
            if (count($row) > 0) {
                $keyboard['inline_keyboard'][] = $row;
            }
        }
        $this->clientsService->setClientCurrencyInput($clientId);
        $this->sendMessageWithButton($chatId, $keyboard, __('messages.get_currency'), $messageIdToDelete);
    }

    public function handleCurrencySelection($chatId, $clientId, $currency_id, $messageIdToDelete): void
    {
        Redis::set('select_currency_for_' . $clientId, $currency_id);
        $this->processAmount($chatId, $clientId, $messageIdToDelete);
    }

    public function isValidAmount($text, $clientId): bool
    {
        return is_numeric($text);
    }

    public function processAmount($chatId, $clientId, $messageIdToDelete): void
    {
//        $keyboard = [
//            'keyboard' => [
//                [
//                    ['text' => __('buttons.to_main')]
//                ],
//                [
//                    ['text' => __('buttons.consultation')]
//                ],
//            ],
//            'resize_keyboard' => true,
//            'one_time_keyboard' => true,
//        ];

        $this->clientsService->setClientAmountInput($clientId);

//        $this->sendMessageWithButton($chatId, $keyboard, __('messages.enter_the_amount_only_numbers'), $messageIdToDelete);
        $this->sendMessage($chatId, __('messages.enter_the_amount_only_numbers'), $messageIdToDelete);
    }

    public function checkProcessAmount($chatId, $clientId, $amount): void
    {
        $credential = $this->credentialService->checkAmountInput($amount);

        $keyboard = [
            'keyboard' => [
                [
                    ['text' => __('buttons.to_main')]
                ],
                [
                    ['text' => __('buttons.consultation')]
                ],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ];
        $currency_id = Redis::get('select_currency_for_' . $clientId);
        Redis::set('select_amount_for_' . $clientId, $amount);
        $currency_name = Currency::find($currency_id)->name;
        $this->clientsService->setClientAmountSuccessInput($clientId);
        $this->sendMessageWithButton($chatId, $keyboard, 'Отправьте ' . $amount . ' ' . $currency_name . ' на этот реквизит ' . $credential . '. ' . __('messages.enter_the_amount_screenshot'));
    }

    public function sendInvalidAmountMessage($chatId, $messageIdToDelete): void
    {
        $this->sendMessage($chatId, __('messages.enter_the_amount_correct_numbers'), $messageIdToDelete);
    }

    public function sendConsultationMessage($chatId, $clientId): void
    {
        $this->clientsService->setClientConsultationInput($clientId);

        $keyboard = [
            'keyboard' => [
                [
                    ['text' => __('buttons.to_main')],
                ],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ];
        $this->sendMessageWithButton($chatId, $keyboard, __('messages.consultation'));
    }

    public function getAndSavePhoto($photo, $clientId, $chatId): void
    {
        $this->setClientLanguage($this->clientsService->getClientLanguage($clientId));
        $amount = Redis::get('select_amount_for_' . $clientId);
        $currency_id = Redis::get('select_currency_for_' . $clientId);
        $file_id = $photo['file_id'];
        $file_url = $this->url . "/getFile?file_id=" . $file_id;
        $response = Http::get($file_url);

        if ($response->successful()) {
            $data = $response->json();
            if ($data['ok']) {

                $file_path = $data['result']['file_path'];
                $download_url = "https://api.telegram.org/file/bot" . config('telegram.telegram_bot.token') . "/$file_path";
                $imageContent = Http::get($download_url);
//                $client = $this->clientsService->getClient($clientId);

                $this->clientsService->setClientSendScreenshot($clientId);
                $order = $this->orderService->saveOrder($chatId, $clientId, $amount, $currency_id);
                Redis::set('save_order_id_for' . $clientId, $order->id);

                $order->addMediaFromString($imageContent->body())
                    ->usingFileName('screenshot.jpg')
                    ->toMediaCollection('amount_check');

                $keyboard = [
                    'keyboard' => [
                        [
                            ['text' => __('buttons.cancel')],
                        ],
                    ],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true,
                ];
                $this->sendMessageWithButton($chatId, $keyboard, 'Чек принят и ушел в разработку. Ждите ответ от менеджера');
            } else {
                Log::error('Не удалось получить файл');
            }
        } else {
            Log::error('Ошибка запроса к Telegram API');
        }

    }

    public function sendMessage($chatId, $text, $messageIdToDelete = null): void
    {
        $response = Http::post($this->url . "/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
        ]);

        if ($response->failed()) {
            Log::error('Failed to send message: ' . $response->body());
        }

        if ($messageIdToDelete) {
            $this->deleteMessages($chatId, $messageIdToDelete);
        }
    }

    public function sendMessageWithButton($chatId, $keyboard, $text, $messageIdToDelete = null): void
    {
        $response = Http::post($this->url . "/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
//            'reply_markup' => json_encode([
//                'remove_keyboard' => true
//            ]),
            'reply_markup' => json_encode($keyboard),
        ]);

        if ($response->failed()) {
            Log::error('Failed to send message: ' . $response->body());
        }

        // Если передан $messageIdToDelete для удаления, удаляем старое сообщение
        if ($messageIdToDelete) {
            $this->deleteMessages($chatId, $messageIdToDelete);
        }
    }

    public function deleteMessages($chatId, $messageIdToDelete): void
    {
        // Удаляем сообщение с переданным message_id
        $this->deleteMessage($chatId, $messageIdToDelete);

        // Удаляем сообщение с message_id - 1
        $this->deleteMessage($chatId, $messageIdToDelete - 1);
        $this->deleteMessage($chatId, $messageIdToDelete - 2);
    }

    public function deleteMessage($chatId, $messageIdToDelete): void
    {
        $response = Http::post($this->url . "/deleteMessage", [
            'chat_id' => $chatId,
            'message_id' => $messageIdToDelete,
        ]);

        if ($response->failed()) {
//            Log::error('Failed to delete message: ' . $response->body());
        }
    }
}


