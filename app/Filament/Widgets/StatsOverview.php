<?php

namespace App\Filament\Widgets;

use App\Models\Listing;
use App\Models\Review;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('إجمالي المنشآت', Listing::count())
                ->description('المطاعم وشركات السياحة')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('primary'),

            Stat::make('مراجعات قيد المراجعة', Review::where('status', 'pending')->count())
                ->description('بانتظار الموافقة')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('إجمالي المستخدمين', User::count())
                ->description('المستخدمون المسجّلون')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
        ];
    }
}
