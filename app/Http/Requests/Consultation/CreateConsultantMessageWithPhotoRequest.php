<?php

namespace App\Http\Requests\Consultation;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateConsultantMessageWithPhotoRequest extends BaseRequest
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
            'photo' => 'required|file|image|max:10120',
            'chatId' => 'required|integer',
            'caption' => 'nullable|string',
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
            'photo.required' => 'Картинка обязательна.',
            'photo.file' => 'Файл должен быть корректным.',
            'photo.image' => 'Файл должен быть изображением (jpeg, png, bmp, gif или svg).',
            'photo.max' => 'Изображение не должно превышать 10 МБ.',

            'chatId.required' => 'Идентификатор чата обязателен.',
            'chatId.integer' => 'Идентификатор чата должен быть числом.',

            'caption.string' => 'Подпись должна быть строкой.',
        ];
    }

    /**
     * Чистое получение свойств.
     *
     */
    public function getPhotoPatch(): object
    {
        return $this->file('photo');
    }
    public function getChatId(): int
    {
        return (int) $this->input('chatId');
    }
    public function getCaption(): null|string
    {
        return $this->input('caption');
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422));
    }
}
