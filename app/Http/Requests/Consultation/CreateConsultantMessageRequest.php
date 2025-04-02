<?php

namespace App\Http\Requests\Consultation;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class CreateConsultantMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'message' => 'required|string',
        ];
    }

    public function getMessage(): string
    {
        return $this->input('message');
    }
}
