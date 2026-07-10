<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SocialAuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'provider' => ['required', 'string', 'in:google,apple'],
            'id_token' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'provider.required' => 'يجب تحديد مزوّد الدخول.',
            'provider.in' => 'مزوّد الدخول غير مدعوم.',
            'id_token.required' => 'رمز الدخول مطلوب.',
        ];
    }
}
