<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Models\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationGroup = 'الجغرافيا';

    protected static ?string $navigationLabel = 'الدول';

    protected static ?string $modelLabel = 'دولة';

    protected static ?string $pluralModelLabel = 'الدول';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name_ar')
                    ->label('الاسم بالعربية')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name_en')
                    ->label('الاسم بالإنجليزية')
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->label('رمز الدولة (ISO)')
                    ->required()
                    ->maxLength(2),
                Forms\Components\TextInput::make('phone_code')
                    ->label('رمز الاتصال')
                    ->maxLength(8),
                Forms\Components\TextInput::make('flag_emoji')
                    ->label('العلم')
                    ->maxLength(16),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('flag_emoji')
                    ->label('العلم'),
                Tables\Columns\TextColumn::make('name_ar')
                    ->label('الاسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('الرمز')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cities_count')
                    ->label('عدد المدن')
                    ->counts('cities'),
            ])
            ->defaultSort('name_ar')
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
            'index' => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            'edit' => Pages\EditCountry::route('/{record}/edit'),
        ];
    }
}
