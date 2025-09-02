<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateOrderMessageRequest extends BaseRequest
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
            'message' => 'required',
            'isRequisite' => 'required|boolean',
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
            'message.required' => 'Пожалуйста, введите сообщение.',
            'isRequisite.required' => 'Тип сообщения должен отправляться.',
            'isRequisite.boolean' => 'Тип сообщения должен быть bool.'
        ];
    }

    /**
     * Чистое получение свойств.
     *
     * @return <string, string>
     */
    public function getMessage(): string
    {
        return $this->input('message');
    }
    public function getIsRequisite(): string
    {
        return $this->input('isRequisite');
    }
    public function getRequisiteType(): string
    {
        return $this->input('typeRequisite');
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422));
    }
}
