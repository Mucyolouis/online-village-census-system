<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CitizensByGenderChart extends ChartWidget
{
    protected static ?string $heading = 'Citizens by Gender';

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && ($user->hasRole('coc') || $user->hasRole('super_admin'));
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $data = User::groupBy('gender')
            ->select('gender', DB::raw('count(*) as count'))
            ->pluck('count', 'gender')
            ->toArray();

        return [
            'datasets' => [
                [
                    'data' => array_values($data),
                    'backgroundColor' => ['#2196F3', '#FFC107'], // Blue for male, Yellow for female
                ],
            ],
            'labels' => array_map('ucfirst', array_keys($data)), // Capitalize first letter of gender
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}