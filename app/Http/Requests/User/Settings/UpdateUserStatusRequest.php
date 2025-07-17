<?php

namespace App\Http\Requests\User\Settings;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserStatusRequest extends BaseRequest
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
            'userId' => $this->route('userId'),
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
            'userId' => 'required|integer',
            'statusId' => 'required|integer',
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
            'userId.required' => 'ID пользователя обязателен.',
            'userId.integer' => 'ID пользователя должен быть числом.',
            'statusId.required' => 'ID статуса обязателен.',
            'statusId.integer' => 'ID статуса должен быть числом.',
        ];
    }

    /**
     * Чистое получение свойств.
     */
    public function getStatusId(): int
    {
        return (int) $this->input('statusId');
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422));
    }
}
