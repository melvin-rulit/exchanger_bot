<?php

namespace App\Http\Requests\User\Settings;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SaveUserPhotoRequest extends BaseRequest
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

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422));
    }
}
