<?php

namespace App\Filament\Resources\ListingResource\Pages;

use App\Filament\Pages\ImportListings;
use App\Filament\Resources\ListingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListListings extends ListRecords
{
    protected static string $resource = ListingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('import')
                ->label('استيراد بالجملة')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->url(fn (): string => ImportListings::getUrl()),
            Actions\CreateAction::make(),
        ];
    }
}
