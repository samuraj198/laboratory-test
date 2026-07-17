<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|between:2,100',
            'phone' => 'required|string|max:20',
            'email' => 'required|string|email|max:255',
            'comment' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Имя обязательно для заполнения',
            'name.between' => 'Длина имени должна состоять минимум из 2, но максимум из 100 символов',
            'phone.required' => 'Номер телефона обязателен для заполнения',
            'phone.max' => 'Максимально возможная длина номера 20 символов',
            'email.required' => 'Почта обязательна для заполнения',
            'email.email' => 'Неверный формат почты',
            'email.max' => 'Максимально возможная длина почты 255 символов',
            'comment.required' => 'Комментарий обязателен для заполнения',
            'comment.max' => 'Максимально возможная длина комментария 255 символов'
        ];
    }
}
