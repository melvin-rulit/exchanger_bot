<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CloseOrderRequest extends BaseRequest
{
    /**
     * Определяет, авторизован ли пользователь на выполнение запроса.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Laravel будет валидировать и данные из маршрута.
     *
     * @return array<string, string>
     */
    public function validationData(): array
    {
        return array_merge($this->all(), [
            'orderId' => $this->route('orderId'),
        ]);
    }

    /**
     * Правила валидации для запроса.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'selectedOrder.id' => 'required|integer|exists:orders,id',
            'selectedOrder.client.id' => 'required|integer|exists:clients,id',
            'selectedUser.id' => 'required|integer|exists:users,id',
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
            'selectedOrder.id.required' => 'ID заказа обязателен.',
            'selectedUser.id.required' => 'ID клиента обязателен.',
            'selectedOrder.client.id.required' => 'ID клиента обязателен.',
            'selectedOrder.id.integer' => 'ID должен быть целым числом.',
            'selectedUser.id.integer' => 'ID должен быть целым числом.',
            'selectedOrder.client.id.integer' => 'ID должен быть целым числом.',
        ];
    }

    /**
     * Чистое получение свойств.
     */
    public function getOrderId(): int
    {
        return (int) $this->input('selectedOrder.id');
    }
    public function getUserId(): int
    {
        return (int) $this->input('selectedUser.id');
    }
    public function getClientId(): int
    {
        return (int) $this->input('selectedOrder.client.id');
    }

    public function getStatus(): string
    {
        return $this->input('selectedOrder.status');
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422));
    }
}
