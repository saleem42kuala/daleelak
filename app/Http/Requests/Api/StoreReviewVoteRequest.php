<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewVoteRequest extends FormRequest
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
            'review_id' => ['required', 'integer', 'exists:reviews,id'],
            'is_helpful' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'review_id.required' => 'يجب تحديد المراجعة.',
            'review_id.exists' => 'المراجعة غير موجودة.',
            'is_helpful.required' => 'يجب تحديد نوع التصويت.',
            'is_helpful.boolean' => 'قيمة التصويت غير صحيحة.',
        ];
    }
}
