<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Village;
use App\Models\TransferRequest;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    //protected int $sort = -1; // This ensures the widget is displayed first

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && ($user->hasRole('coc') || $user->hasRole('super_admin'));
    }
    protected function getStats(): array
    {
        return [
            Stat::make('Total Citizens', User::count())
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Total Villages', Village::count())
                ->description('Registered villages')
                ->descriptionIcon('heroicon-m-home')
                ->color('primary')
                ->chart([3, 5, 7, 8, 10, 11, 12]),

            Stat::make('Total Roles', Role::count())
                ->description('System roles')
                ->descriptionIcon('heroicon-m-identification')
                ->color('warning')
                ->chart([2, 2, 3, 3, 4, 4, 5]),

            Stat::make('Transfer Requests', TransferRequest::count())
                ->description('Pending transfers')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('danger')
                ->chart([1, 3, 2, 4, 3, 5, 4]),
        ];
    }
}
