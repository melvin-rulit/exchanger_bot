<?php

namespace App\Http\Requests\User\Settings;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class NotificationRequest extends BaseRequest
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
            'notification.id' => ['required', 'integer', 'exists:user_settings,id'],
            'notification.key' => ['required', 'string', 'in:notification'],
            'notification.is_active' => ['required', 'boolean'],
            'notification.is_used' => ['integer', 'in:0,1'],
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

        ];
    }

    /**
     * Чистое получение свойств.
     */
    public function getSettingId(): int
    {
        return (int) $this->input('notification.id');
    }
    public function getSettingKey(): string
    {
        return (string) $this->input('notification.key');
    }
    public function getSettingIsActive(): bool
    {
        return (bool) data_get($this->all(), 'notification.is_active', false);
    }

    public function getSettingIsUsed(): bool
    {
        return (bool) $this->input('notification.is_used');
    }


    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422));
    }
}
