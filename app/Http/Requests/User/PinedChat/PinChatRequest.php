<?php

namespace App\Http\Requests\User\PinedChat;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PinChatRequest extends BaseRequest
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
            'orderId' => 'required|integer',
            'clientId' => 'required|integer',
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
            'orderId.required' => 'ID заказа обязателен.',
            'orderId.integer' => 'ID заказа должен быть числом.',
            'clientId.required' => 'ID клиента обязателен.',
            'clientId.integer' => 'ID клиента должен быть числом.',
        ];
    }

    /**
     * Чистое получение свойств.
     */
    public function getOrderId(): int
    {
        return (int) $this->input('orderId');
    }
    public function getClientId(): int
    {
        return (int) $this->input('clientId');
    }


    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422));
    }
}
