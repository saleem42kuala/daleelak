<x-filament-panels::page>
    <form wire:submit.prevent="preview">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit" icon="heroicon-o-eye" wire:loading.attr="disabled" wire:target="preview">
                <span wire:loading.remove wire:target="preview">معاينة</span>
                <span wire:loading wire:target="preview">جارٍ القراءة...</span>
            </x-filament::button>
        </div>
    </form>

    @if ($previewed)
        <x-filament::section class="mt-6">
            <x-slot name="heading">
                سيتم استيراد {{ $previewCount }} منشأة
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right rtl">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400">
                            <th class="px-3 py-2 font-medium">الاسم</th>
                            <th class="px-3 py-2 font-medium">العنوان</th>
                            <th class="px-3 py-2 font-medium">الهاتف</th>
                            <th class="px-3 py-2 font-medium">التقييم</th>
                            <th class="px-3 py-2 font-medium">المراجعات</th>
                            <th class="px-3 py-2 font-medium">حلال</th>
                            <th class="px-3 py-2 font-medium">عائلي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($preview as $row)
                            <tr class="border-b border-gray-100 dark:border-white/5">
                                <td class="px-3 py-2">{{ $row['name_ar'] }}</td>
                                <td class="px-3 py-2">{{ $row['address_ar'] ?? '—' }}</td>
                                <td class="px-3 py-2" dir="ltr">{{ $row['phone'] ?? '—' }}</td>
                                <td class="px-3 py-2">{{ $row['overall_rating'] }}</td>
                                <td class="px-3 py-2">{{ $row['reviews_count'] }}</td>
                                <td class="px-3 py-2">{{ $row['is_halal'] ? 'نعم' : 'لا' }}</td>
                                <td class="px-3 py-2">{{ $row['is_family'] ? 'نعم' : 'لا' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if ($previewCount > count($preview))
                    <p class="mt-3 text-xs text-gray-500">
                        تُعرض أول {{ count($preview) }} منشأة فقط من أصل {{ $previewCount }}.
                    </p>
                @endif
            </div>

            <div class="mt-6">
                <x-filament::button
                    wire:click="import"
                    color="success"
                    icon="heroicon-o-arrow-up-tray"
                    wire:loading.attr="disabled"
                    wire:target="import"
                >
                    <span wire:loading.remove wire:target="import">استيراد الآن</span>
                    <span wire:loading wire:target="import">جارٍ الاستيراد...</span>
                </x-filament::button>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
