<?php

namespace App\Filament\Filters;

use Filament\Forms\Components\Select;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class AgeFilter extends Filter
{
    protected function setUp(): void
    {
        $this->form([
            Select::make('age_range')
                ->options([
                    '0-18' => '0-18 years',
                    '19-30' => '19-30 years',
                    '31-50' => '31-50 years',
                    '51+' => '51+ years',
                ])
                ->placeholder('Select Age Range')
        ]);
    }

    public function apply(Builder $query, array $data): Builder
    {
        if (isset($data['age_range'])) {
            [$min, $max] = explode('-', $data['age_range'] . '-999');
            $now = now();
            return $query->whereDate('date_of_birth', '<=', $now->subYears($min)->format('Y-m-d'))
                         ->whereDate('date_of_birth', '>', $now->subYears($max + 1)->format('Y-m-d'));
        }

        return $query;
    }
}