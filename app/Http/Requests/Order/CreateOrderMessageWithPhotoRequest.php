<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateOrderMessageWithPhotoRequest extends BaseRequest
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
