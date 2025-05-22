<?php

namespace App\Http\Requests\User\PinedChat;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UnPinChatRequest extends BaseRequest
{
    /**
     * Определяет, авторизован ли пользователь на выполнение запроса.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Правила валидации для запроса.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'chatId' => 'required|integer',
        ];
    }

    /**
     * Кастомные сообщения об ошибках валидации.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'chatId.required' => 'ID чата обязателен.',
            'chatId.integer' => 'ID чата должен быть числом.',
        ];
    }

    /**
     * Чистое получение свойств.
     */
    public function getChatId(): int
    {
        return (int) $this->input('chatId');
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422));
    }
}
