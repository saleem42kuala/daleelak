<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReviewRequest extends FormRequest
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
            'listing_id' => [
                'required',
                'integer',
                Rule::exists('listings', 'id')->where('is_active', true),
                Rule::unique('reviews', 'listing_id')
                    ->where(fn ($q) => $q->where('user_id', $this->user()?->id)),
            ],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment_ar' => ['required', 'string', 'min:3', 'max:2000'],
            'criteria' => ['sometimes', 'array'],
            'criteria.*.criteria_id' => ['required_with:criteria', 'integer', 'exists:criteria,id'],
            'criteria.*.value' => ['required_with:criteria', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'listing_id.required' => 'يجب تحديد المنشأة.',
            'listing_id.exists' => 'المنشأة غير موجودة أو غير متاحة.',
            'listing_id.unique' => 'لقد قمت بمراجعة هذه المنشأة من قبل.',
            'rating.required' => 'التقييم مطلوب.',
            'rating.integer' => 'يجب أن يكون التقييم رقماً صحيحاً.',
            'rating.min' => 'أقل تقييم هو نجمة واحدة.',
            'rating.max' => 'أعلى تقييم هو خمس نجوم.',
            'comment_ar.required' => 'التعليق مطلوب.',
            'comment_ar.min' => 'التعليق قصير جداً.',
            'comment_ar.max' => 'التعليق طويل جداً.',
            'criteria.array' => 'صيغة المعايير غير صحيحة.',
            'criteria.*.criteria_id.exists' => 'أحد المعايير المحددة غير موجود.',
            'criteria.*.value.boolean' => 'قيمة المعيار يجب أن تكون صحيحة أو خاطئة.',
        ];
    }
}
