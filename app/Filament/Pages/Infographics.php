<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Filament\Widgets\CitizensByGenderChart;
use App\Filament\Widgets\CitizensByVillageChart;

class Infographics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string $view = 'filament.pages.infographics';

    protected static ?string $navigationLabel = 'Infographics';

    protected static ?string $title = 'Infographics';
    protected int $sort = 2; // This ensures the widget is displayed second

    protected function getHeaderWidgets(): array
    {
        return [
            CitizensByVillageChart::class,
            CitizensByGenderChart::class,
        ];
    }
    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && ($user->hasRole('coc') || $user->hasRole('super_admin'));
    }
}