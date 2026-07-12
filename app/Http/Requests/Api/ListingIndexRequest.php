<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ListingIndexRequest extends FormRequest
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
            'country_id' => ['sometimes', 'integer', 'exists:countries,id'],
            'city_id' => ['sometimes', 'integer', 'exists:cities,id'],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'criteria' => ['sometimes', 'array'],
            'criteria.*' => ['string', 'exists:criteria,key'],
            'search' => ['sometimes', 'string', 'max:255'],
            'sort' => ['sometimes', 'string', 'in:top_rated,nearest,most_reviewed'],
            'lat' => ['required_if:sort,nearest', 'numeric', 'between:-90,90'],
            'lng' => ['required_if:sort,nearest', 'numeric', 'between:-180,180'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'country_id.exists' => 'الدولة المحددة غير موجودة.',
            'city_id.exists' => 'المدينة المحددة غير موجودة.',
            'category_id.exists' => 'التصنيف المحدد غير موجود.',
            'sort.in' => 'قيمة الترتيب غير صحيحة.',
            'lat.required_if' => 'خط العرض مطلوب عند الترتيب حسب الأقرب.',
            'lng.required_if' => 'خط الطول مطلوب عند الترتيب حسب الأقرب.',
            'criteria.*.exists' => 'أحد المعايير المحددة غير موجود.',
        ];
    }
}
