<?php

namespace App\Telegram\Traits;

use App\Exceptions\TelegramApiException;
use App\Exceptions\Images\MediaLibraryException;

trait SavePhoto
{
    protected string $url;

    /**
     * @throws TelegramApiException
     * @throws MediaLibraryException
     */
    public function savePhotoToConsultant(array $photo, int $clientId, int $chatId, string $message_group, ?int $orderId = null): void
    {
        $response = $this->getTelegramFileContent($photo['file_id']);

        if ($response) {
            $imageContent = $response->body();

            $messageModel = $this->chatService->prepareSaveMessage($chatId, $clientId, $message_group, $photo['file_id'], null, $orderId);

            $this->saveImageToModelFromResponse($imageContent, 'screenshot.jpg', $messageModel, 'chat_screenshot');
        }
    }
}
