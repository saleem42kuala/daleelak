<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'المستخدمون';

    protected static ?string $navigationLabel = 'المستخدمون';

    protected static ?string $modelLabel = 'مستخدم';

    protected static ?string $pluralModelLabel = 'المستخدمون';

    /**
     * Users are created through social sign-in, not the admin panel.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    protected static function isSelf(?User $record): bool
    {
        return $record !== null && $record->id === auth()->id();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('الاسم')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('phone')
                    ->label('رقم الهاتف')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\Select::make('country_id')
                    ->label('الدولة')
                    ->relationship('country', 'name_ar')
                    ->searchable()
                    ->preload(),
                Forms\Components\Toggle::make('is_admin')
                    ->label('مدير النظام')
                    ->helperText(fn (?User $record): ?string => static::isSelf($record)
                        ? 'لا يمكنك تغيير صلاحية حسابك الحالي.'
                        : null)
                    ->disabled(fn (?User $record): bool => static::isSelf($record)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('country.name_ar')
                    ->label('الدولة')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_admin')
                    ->label('مدير')
                    ->boolean(),
                Tables\Columns\TextColumn::make('banned_at')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => $state ? 'محظور' : 'نشط')
                    ->color(fn ($state): string => $state ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_admin')
                    ->label('مدير النظام')
                    ->trueLabel('المدراء فقط')
                    ->falseLabel('المستخدمون العاديون')
                    ->placeholder('الجميع'),
                Tables\Filters\SelectFilter::make('country_id')
                    ->label('الدولة')
                    ->relationship('country', 'name_ar')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('toggleBan')
                    ->label(fn (User $record): string => $record->isBanned() ? 'تفعيل' : 'حظر')
                    ->icon(fn (User $record): string => $record->isBanned() ? 'heroicon-o-check-circle' : 'heroicon-o-no-symbol')
                    ->color(fn (User $record): string => $record->isBanned() ? 'success' : 'danger')
                    // Hidden for the current admin to prevent self-lockout.
                    ->visible(fn (User $record): bool => ! static::isSelf($record))
                    ->requiresConfirmation()
                    ->modalHeading(fn (User $record): string => $record->isBanned() ? 'تفعيل المستخدم' : 'حظر المستخدم')
                    ->action(function (User $record): void {
                        $record->update(['banned_at' => $record->isBanned() ? null : now()]);
                    })
                    ->successNotificationTitle('تم تحديث حالة المستخدم.'),
                Tables\Actions\EditAction::make()
                    ->label('تعديل'),
                Tables\Actions\DeleteAction::make()
                    ->label('حذف')
                    // Cannot delete the currently logged-in admin.
                    ->visible(fn (User $record): bool => ! static::isSelf($record)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
