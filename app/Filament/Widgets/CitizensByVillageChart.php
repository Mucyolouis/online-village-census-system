<?php

namespace App\Filament\Widgets;

use App\Models\Village;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class CitizensByVillageChart extends ChartWidget
{
    protected static ?string $heading = 'Citizens by Village';

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && ($user->hasRole('coc') || $user->hasRole('super_admin'));
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $data = Village::withCount('users')
            ->orderBy('users_count', 'desc')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Number of Citizens',
                    'data' => $data->pluck('users_count')->toArray(),
                    'backgroundColor' => '#4CAF50', // Green color
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Number of Citizens',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Villages',
                    ],
                ],
            ],
        ];
    }
}