<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Services\ListingImporter;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportListings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static string $view = 'filament.pages.import-listings';

    protected static ?string $title = 'استيراد منشآت';

    protected static ?string $slug = 'import-listings';

    // Reached via the Listings header action, not the sidebar.
    protected static bool $shouldRegisterNavigation = false;

    /** @var array<string, mixed> */
    public ?array $data = [];

    /** @var array<int, array<string, mixed>> first rows shown in the preview table */
    public array $preview = [];

    public int $previewCount = 0;

    public bool $previewed = false;

    /** Admin-only (the whole panel is already admin-gated; explicit for clarity). */
    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->is_admin;
    }

    public function mount(): void
    {
        $this->form->fill(['skip_duplicates' => true]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('إعدادات الاستيراد')
                    ->schema([
                        Select::make('country_id')
                            ->label('الدولة')
                            ->options(Country::orderBy('name_ar')->pluck('name_ar', 'id'))
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('city_id', null))
                            ->required(),
                        Select::make('city_id')
                            ->label('المدينة')
                            ->options(fn (callable $get) => $get('country_id')
                                ? City::where('country_id', $get('country_id'))->orderBy('name_ar')->pluck('name_ar', 'id')
                                : [])
                            ->searchable()
                            ->required(),
                        Select::make('category_id')
                            ->label('التصنيف')
                            ->options(Category::whereIn('key', ['restaurant', 'tourism_company'])->pluck('name_ar', 'id'))
                            ->required(),
                        Toggle::make('skip_duplicates')
                            ->label('تجاهل التكرارات (بالاسم والمدينة)')
                            ->default(true),
                        Textarea::make('default_description')
                            ->label('الوصف الافتراضي')
                            ->helperText('يُستخدم عندما لا يحتوي السجل على وصف.')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('الملف')
                    ->schema([
                        FileUpload::make('file')
                            ->label('ملف الاستيراد (JSON / CSV / Excel)')
                            ->disk('local')
                            ->directory('imports')
                            ->visibility('private')
                            ->acceptedFileTypes([
                                'application/json',
                                'text/csv',
                                'application/csv',
                                'text/plain',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->maxSize(5120) // 5 MB
                            ->required()
                            ->helperText('الحد الأقصى لحجم الملف 5 ميجابايت.')
                            ->validationMessages([
                                'required' => 'يجب اختيار ملف للاستيراد.',
                                'max' => 'حجم الملف يجب ألا يتجاوز 5 ميجابايت.',
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('template')
                ->label('تحميل قالب CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(fn (): StreamedResponse => $this->downloadTemplate()),
        ];
    }

    public function preview(): void
    {
        $state = $this->form->getState();

        try {
            $rows = $this->readRows($state);
        } catch (\Throwable $e) {
            Notification::make()->title($e->getMessage())->danger()->send();

            return;
        }

        if (count($rows) === 0) {
            $this->previewed = false;
            Notification::make()->title('لم يتم العثور على أي منشآت صالحة في الملف.')->danger()->send();

            return;
        }

        $this->previewCount = count($rows);
        $this->preview = array_slice($rows, 0, 50);
        $this->previewed = true;
    }

    public function import(): void
    {
        $state = $this->form->getState();

        try {
            $rows = $this->readRows($state);
        } catch (\Throwable $e) {
            Notification::make()->title($e->getMessage())->danger()->send();

            return;
        }

        if (count($rows) === 0) {
            Notification::make()->title('لا توجد منشآت للاستيراد.')->danger()->send();

            return;
        }

        $result = app(ListingImporter::class)->import($rows, $state, auth()->user());

        $this->deleteUploaded($state);

        Notification::make()
            ->title("تم استيراد {$result['imported']} منشأة بنجاح")
            ->body($result['skipped'] > 0 ? "تم تجاهل {$result['skipped']} منشأة مكررة." : null)
            ->success()
            ->send();

        $this->form->fill(['skip_duplicates' => true]);
        $this->preview = [];
        $this->previewCount = 0;
        $this->previewed = false;
    }

    /**
     * @param  array<string, mixed>  $state
     * @return array<int, array<string, mixed>>
     */
    protected function readRows(array $state): array
    {
        $file = $state['file'] ?? null;
        if (is_array($file)) {
            $file = reset($file);
        }
        if (! $file) {
            return [];
        }

        $absolute = Storage::disk('local')->path($file);
        $extension = pathinfo((string) $file, PATHINFO_EXTENSION);

        return app(ListingImporter::class)->parse($absolute, $extension);
    }

    /**
     * @param  array<string, mixed>  $state
     */
    protected function deleteUploaded(array $state): void
    {
        $file = $state['file'] ?? null;
        if (is_array($file)) {
            $file = reset($file);
        }
        if ($file) {
            Storage::disk('local')->delete($file);
        }
    }

    protected function downloadTemplate(): StreamedResponse
    {
        $headers = ['name_ar', 'address_ar', 'phone', 'latitude', 'longitude', 'overall_rating', 'reviews_count', 'is_halal', 'is_family'];
        $example = ['مطعم الشام', 'شارع الملك فهد، الرياض', '+966500000000', '24.7136', '46.6753', '4.5', '120', 'true', 'false'];

        return response()->streamDownload(function () use ($headers, $example) {
            echo "\xEF\xBB\xBF"; // UTF-8 BOM so Excel renders Arabic correctly
            echo implode(',', $headers)."\r\n";
            echo implode(',', $example)."\r\n";
        }, 'listings_import_template.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
