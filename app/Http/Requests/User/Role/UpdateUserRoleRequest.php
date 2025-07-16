<?php

namespace App\Http\Requests\User\Role;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRoleRequest extends BaseRequest
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
            'roleId' => 'required|integer',
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
            'roleId.required' => 'ID роли обязателен.',
            'roleId.integer' => 'ID роли должен быть числом.',
        ];
    }

    /**
     * Чистое получение свойств.
     */
    public function getRoleId(): int
    {
        return (int) $this->input('roleId');
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422));
    }
}
