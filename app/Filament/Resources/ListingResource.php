<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ListingResource\Pages;
use App\Models\Listing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ListingResource extends Resource
{
    protected static ?string $model = Listing::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'المحتوى';

    protected static ?string $navigationLabel = 'المنشآت';

    protected static ?string $modelLabel = 'منشأة';

    protected static ?string $pluralModelLabel = 'المنشآت';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('البيانات الأساسية')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('التصنيف')
                            ->relationship('category', 'name_ar')
                            ->required(),
                        Forms\Components\Select::make('area_id')
                            ->label('المنطقة')
                            ->relationship('area', 'name_ar')
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => $record->name_ar.' — '.$record->city->name_ar)
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('name_ar')
                            ->label('الاسم بالعربية')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name_en')
                            ->label('الاسم بالإنجليزية')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description_ar')
                            ->label('الوصف')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('address_ar')
                            ->label('العنوان')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('phone')
                            ->label('الهاتف')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),
                        Forms\Components\TextInput::make('promotion_rank')
                            ->label('أولوية الترويج')
                            ->helperText('الأولوية في الظهور (رقم أصغر = يظهر أولاً، اتركه فارغاً لعدم الترويج)')
                            ->numeric()
                            ->minValue(1)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('latitude')
                            ->label('خط العرض')
                            ->numeric(),
                        Forms\Components\TextInput::make('longitude')
                            ->label('خط الطول')
                            ->numeric(),
                    ]),

                Forms\Components\Section::make('الصور')
                    ->schema([
                        Forms\Components\Repeater::make('photos')
                            ->label('صور المنشأة')
                            ->relationship()
                            ->schema([
                                Forms\Components\FileUpload::make('path')
                                    ->label('الصورة')
                                    ->image()
                                    ->disk('public')
                                    ->directory('listings')
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Toggle::make('is_cover')
                                    ->label('صورة الغلاف'),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('الترتيب')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->reorderable('sort_order')
                            ->addActionLabel('إضافة صورة'),
                    ]),

                Forms\Components\Section::make('درجات المعايير')
                    ->description('تُحتسب هذه الدرجات تلقائياً من مراجعات الزوار عبر المراقب (Observer). التعديل اليدوي هنا يُعد تجاوزاً إدارياً.')
                    ->schema([
                        Forms\Components\Repeater::make('listingCriteria')
                            ->label('المعايير')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('criteria_id')
                                    ->label('المعيار')
                                    ->relationship('criteria', 'name_ar')
                                    ->required(),
                                Forms\Components\TextInput::make('score')
                                    ->label('الدرجة (%)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->default(0),
                                Forms\Components\TextInput::make('votes_count')
                                    ->label('عدد الأصوات')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->addActionLabel('إضافة معيار'),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_ar')
                    ->label('الاسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name_ar')
                    ->label('التصنيف')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('area.name_ar')
                    ->label('المنطقة')
                    ->description(fn (Listing $record): ?string => $record->area?->city?->name_ar)
                    ->searchable(),
                Tables\Columns\TextColumn::make('overall_rating')
                    ->label('التقييم')
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviews_count')
                    ->label('المراجعات')
                    ->sortable(),
                Tables\Columns\TextColumn::make('promotion_rank')
                    ->label('أولوية الترويج')
                    ->badge()
                    ->color('info')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('التصنيف')
                    ->relationship('category', 'name_ar'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('الحالة'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListListings::route('/'),
            'create' => Pages\CreateListing::route('/create'),
            'edit' => Pages\EditListing::route('/{record}/edit'),
        ];
    }
}
