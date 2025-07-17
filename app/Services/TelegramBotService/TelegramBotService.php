<?php

namespace App\Services\TelegramBotService;

use Exception;
use App\DTO\CallbackData;
use App\DTO\CallbackMenu;
use App\Enums\MenuLevelStatus;
use App\DTO\CallbackTelegramData;
use App\Telegram\Handlers\AbstractTelegramHandler;

class TelegramBotService extends AbstractTelegramHandler implements TelegramBotServiceInterface
{
    /**
     * @throws Exception
     */
    public function getWebchook($data): void
    {
            if (isset($data['callback_query'])) {

                $callback = CallbackTelegramData::fromWebhook($data['callback_query'], true);
                $callbackData = CallbackData::fromTelegram(['callbackQuery' => $callback->callbackData,'clientBotId' => $callback->clientBotId]);
                setAppLanguage($this->clientsService->getClientLanguage($callback->clientBotId));
                $this->callbackRouter->route($callbackData, $callback->clientBotId, $callback->chatId, $callback->messageId);
            }

            $callback = CallbackTelegramData::fromWebhook($data);

            if (isset($data['message']['photo']) || isset($data['message']['document']) && !$this->clientsService->isUserInAmountInput($callback->clientBotId) && !$this->redis->getCountryConsultant($callback->chatId) && !$this->redis->getBankConsultant($callback->chatId) && !$this->redis->getAmountConsultant($callback->chatId) && !$this->redis->getRequisiteConsultant($callback->chatId) && !$this->redis->getWalletConsultant($callback->chatId)) {

                if ($this->clientsService->isClientSendScreenshot($callback->clientBotId)) {

                    if (!empty($callback->photos)) {
                        $this->receiptHandler->prepareSendCheck(
                            getLargestPhoto($callback->photos),
                            $callback->clientBotId,
                            $callback->chatId
                        );
                    } elseif (!empty($callback->document)) {

                        $this->receiptHandler->prepareSendCheck(
                            getDocumentFile($callback->document),
                            $callback->clientBotId,
                            $callback->chatId
                        );
                    }
                }
                if ($this->clientsService->isClientConsultationInput($callback->clientBotId)) {
                    $message_group = $this->redis->getMessageGroupForConsultation($callback->chatId);
                    $this->savePhoto(getLargestPhoto($callback->photos), $this->redis->getClientIdForChat($callback->chatId), $callback->chatId, $message_group);
                }

        } elseif (isset($data['message']['text']) || isset($data['message']['photo'])) {
//            } elseif (isset($data['message']['text'])) {

                $this->clientsService->checkIfClientExit($data);
                setAppLanguage($this->clientsService->getClientLanguage($callback->clientBotId));

                if ($this->clientsService->isClientInACountryInput($callback->clientBotId)) {
                    $inConsultation = $this->redis->getCountryConsultant($callback->chatId);
                    $this->consultationHandles->handleTextMessageIfInConsultation($data, $callback, $inConsultation);
                }
                elseif ($this->clientsService->isClientInBankInput($callback->clientBotId)) {
                    $inConsultation = $this->redis->getBankConsultant($callback->chatId);
                    $this->consultationHandles->handleTextMessageIfInConsultation($data, $callback, $inConsultation);
                }
                elseif ($this->clientsService->isUserInAmountInput($callback->clientBotId)) {
                    $inConsultation = $this->redis->getAmountConsultant($callback->chatId);
                    $this->consultationHandles->handleTextMessageIfInConsultation($data, $callback, $inConsultation, null, MenuLevelStatus::Amount->value);
                }
                elseif ($this->clientsService->isUserInRequisiteInput($callback->clientBotId)) {
                    $inConsultation = $this->redis->getRequisiteConsultant($callback->chatId);
                    $this->consultationHandles->handleTextMessageIfInConsultation($data, $callback, $inConsultation, $this->redisField->getOrderId($callback->chatId));
                }
                elseif ($callback->text === __('buttons.change_language') && !$this->clientsService->isClientInACountryInput($callback->clientBotId) && !$this->startHandles->checkMainMenu($callback->text) && !$this->clientsService->isUserInAmountInput($callback->clientBotId)) {
                    $this->languageMessageHandler->handle($callback->chatId, $callback->clientBotId, $callback->messageId);
                }
//            elseif (!$this->startHandles->checkMainMenu($callback->text) && $this->clientsService->isClientSendScreenshot($callback->clientBotId)) {
//
//            }
                elseif (!$this->startHandles->checkMainMenu($callback->text) && $this->clientsService->isClientWalletInput($callback->clientBotId)) {
                    $inConsultation = $this->redis->getWalletConsultant($callback->chatId);
                    if (!$inConsultation) {
                        $this->chatService->prepareSaveMessage(
                            $callback->chatId,
                            $this->clientsService->getClient($callback->chatId, $callback->clientBotId),
                            $this->redis->getMessageGroupForConsultation($callback->chatId),
                            null,
                            $callback->text,
                            $this->redisField->getOrderId($callback->chatId));

                        $this->telegramMessageService->sendMessage($callback->chatId, __('messages.wait_usdt'));
                    }

                    $this->consultationHandles->handleTextMessageIfInConsultation($data, $callback, $inConsultation, $this->redisField->getOrderId($callback->chatId));
                }
                elseif (!$this->startHandles->checkMainMenu($callback->text) && $this->clientsService->isClientConsultationInput($callback->clientBotId)) {

//               if ($callback->text === __('buttons.to_main')) {
//                   \Log::info($callback->text);
//               }
                    $message = $this->chatService->prepareSaveMessage(
                        $callback->chatId,
                        $this->clientsService->getClient($callback->chatId, $callback->clientBotId),
                        $this->redis->getMessageGroupForConsultation($callback->chatId),
                        null,
                        $callback->text);

                    if ($message) {
                        $this->redis->setMessageIdForConsultationFromClient($callback->chatId, $message->id, 0);
                    }
                }
                else {
                    if (isset($callback->text)) {
                        if ($this->startHandles->checkStartMessage($callback->text, $callback) || $this->startHandles->checkMainMenu($callback->text)) {
                            $this->startHandles->sendStartMessageWithButtons($callback->chatId, $callback->clientBotId, $callback->messageId);
                        } else {
                            $callbackData = CallbackMenu::fromTelegram(['callbackMenuKey' => $callback->text, 'clientBotId' => $callback->clientBotId]);
                            $this->callbackMenuRouter->route($callbackData, $callback->clientBotId, $callback->chatId, $callback->messageId);
                        }
                    }
                }
            }
        }
}
