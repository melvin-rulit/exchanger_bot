<?php

namespace App\Http\Requests\Template;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTemplateMessageRequest extends BaseRequest
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
            'templateMessage' => 'required',
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
            'templateMessage.required' => 'Название шаблона обязателено.',
        ];
    }

    /**
     * Чистое получение свойств.
     */
    public function getTemplateId(): int
    {
        return (int) $this->input('templateId');
    }
    public function getTemplate(): string
    {
        return (string) $this->input('templateMessage');
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422));
    }
}
