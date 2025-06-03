<?php

namespace App\Telegram\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use App\Exceptions\TelegramApiException;
use App\Exceptions\Images\MediaLibraryException;
use App\Exceptions\Images\ImageDownloadException;

trait HandlesFile
{
    /**
     * Подготовка ссылки для скачивания
     * @throws TelegramApiException
     */

    public function getTelegramFileContent(string $fileId): ?Response
    {
        try {
            $fileInfoUrl = $this->url . "/getFile?file_id=" . $fileId;
            $response = Http::get($fileInfoUrl);

            if (!$response->successful()) {
                throw new TelegramApiException("Не удалось получить информацию о файле Telegram: " . $response->body());
            }

            $filePath = $response->json()['result']['file_path'] ?? null;

            if (!$filePath) {
                throw new TelegramApiException("Поле file_path не найдено в ответе Telegram API для file_id: $fileId");
            }

            $url = $this->download_url . "/$filePath";
            return $this->downloadImage($url);

        } catch (\Throwable $e) {
            throw new TelegramApiException("Ошибка при обращении к Telegram API: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Загружает файл по URL и возвращает объект Response или null при ошибке
     * @throws ImageDownloadException
     */
    public function downloadImage(string $url): ?Response
    {
        try {
            $response = Http::get($url);

            if ($response->successful()) {
                return $response;
            }

            throw new ImageDownloadException("Ошибка при загрузке изображения. Ответ: " . $response->body());
        }
        catch (\Throwable $e) {
            throw new ImageDownloadException("Исключение при загрузке изображения: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Сохраняет файл в Media Library (Spatie) из объекта Response
     * @throws MediaLibraryException
     */
    public function saveImageToModelFromResponse(string $imageContent, string $fileName, $model, string $collection = 'default', bool $forceDelete = true): void
    {
        try {
            if ($forceDelete && $model->getMedia($collection)->isNotEmpty()) {
                $this->deleteIssetImages($model, $collection);
            }

            $model->addMediaFromString($imageContent)
                ->usingFileName($fileName)
                ->toMediaCollection($collection);
        } catch (\Throwable $e) {
            throw new MediaLibraryException("Ошибка при сохранении изображения в модель: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Удаляет все предыдущие файлы в коллекции
     * @throws MediaLibraryException
     */
    public function deleteIssetImages($model, $collection): void
    {
        try {
            $model->clearMediaCollection($collection);
        } catch (\Throwable $e) {
            throw new MediaLibraryException("Ошибка при удалении файлов из коллекции '$collection': " . $e->getMessage(), 0, $e);
        }
    }
}
