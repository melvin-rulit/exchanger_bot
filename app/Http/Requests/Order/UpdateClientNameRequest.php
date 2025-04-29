<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateClientNameRequest extends BaseRequest
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
            'first_name' => 'required|string|max:25',
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
            'first_name.required' => 'Пожалуйста, введите имя клиента.',
            'first_name.string' => 'Имя клиента должно быть строкой.',
            'first_name.max' => 'Имя клиента не должно превышать 25 символов.',
        ];
    }

    /**
     * Чистое получение свойств.
     */
    public function getClientName(): string
    {
        return $this->input('first_name');
    }

//    /**
//     * Атрибуты для человеко читаемых ошибок если нет     public function messages(): array.
//     *
//     * @return array<string, string>
//     */
//    public function attributes(): array
//    {
//        return [
//            'first_name' => 'Имя клиента',
//        ];
//    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422));
    }
}
