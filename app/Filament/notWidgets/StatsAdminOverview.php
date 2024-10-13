<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Blog\Post;
use Spatie\Permission\Models\Role;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsAdminOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            
            Stat::make('users',User::query()->count())
                ->description('Total Users')
                ->descriptionIcon('heroicon-o-user')
                ->color('success'),
            Stat::make('users',Post::query()->count()),
            Stat::make('roles',Role::query()->count()),
            Stat::make('users','128k'),
        ];
    }
}
