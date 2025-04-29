<?php

/**
 * Получить самое маленькое фото (первое в списке Telegram API).
 *
 * @param array<int, array> $photos
 * @return array|null
 */

if (!function_exists('getSmallestPhoto')) {
    function getSmallestPhoto(array $photos): ?array
    {
        return reset($photos) ?: null;
    }
}

/**
 * Получить среднее фото (в списке Telegram API).
 *
 * @param array<int, array> $photos
 * @return array|null
 */

if (!function_exists('getMiddlePhoto')) {
    function getMiddlePhoto(array $photos): ?array
    {
        if (empty($photos)) {
            return null;
        }

        $middleIndex = floor((count($photos) - 1) / 2);

        return $photos[$middleIndex] ?? null;
    }
}

/**
 * Получить самое большое фото (последнее в списке Telegram API).
 *
 * @param array<int, array> $photos
 * @return array|null
 */

if (!function_exists('getLargestPhoto')) {
    function getLargestPhoto(array $photos): ?array
    {
        return end($photos) ?: null;
    }
}

