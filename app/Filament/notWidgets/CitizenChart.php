<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Blog\Post;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;


class CitizenChart extends ChartWidget
{
    protected static bool $isLazy = false;
    protected static ?string $heading = 'Citizens';
    protected static ?string $pollingInterval = '10s';
    protected static ?string $maxHeight = '550px';

    protected function getData(): array
{
    $data = Trend::model(User::class)
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->count();
    return [
        'datasets' => [
            [
                'label' => 'Citizen',
                'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
            ],
        ],
        'labels' => $data->map(fn (TrendValue $value) => $value->date),
    ];
}

    protected function getType(): string
    {
        return 'doughnut';
    }
    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }
    public function getDescription(): ?string
{
    return 'The number of citizens registered per month.';
}
}
