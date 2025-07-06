<?php

namespace App\Http\Requests\User\Settings;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DeleteUserRequest extends BaseRequest
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
        ];
    }

    /**
     * Чистое получение свойств.
     */
    public function getName(): string
    {
        return (string) $this->input('userForm.editableUserName');
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422));
    }
}
