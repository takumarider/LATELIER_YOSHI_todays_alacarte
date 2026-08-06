<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'regex:/^0\d{2,3}-\d{3,4}-\d{4}$/'],
            'visit_time' => ['required', 'string', 'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'お名前は必須です。',
            'customer_name.max' => 'お名前は100文字以内で入力してください。',
            'email.required' => 'メールアドレスは必須です。',
            'email.email' => '正しいメールアドレスを入力してください。',
            'phone.regex' => '電話番号はハイフン区切りで入力してください（例: 090-1234-5678）。',
            'visit_time.required' => '来店予定時間は必須です。',
            'visit_time.regex' => '来店時間は HH:MM 形式で入力してください。',
        ];
    }
}
