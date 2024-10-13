<?php

namespace App\Filament\Resources\FamilyResource\Pages;

use Filament\Tables;
use App\Models\Family;
use Filament\Tables\Table;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\BadgeColumn;
use App\Filament\Resources\FamilyResource;

class ViewFamilyMembers extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $resource = FamilyResource::class;

    protected static string $view = 'filament.resources.family-resource.pages.view-family-members';

    public Family $record;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->record->members()->getQuery())
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable()
                ->formatStateUsing(function ($state, $record) {
                    $isHeadOfFamily = $record->id === $this->record->head_of_family_id;
                    $badge = $isHeadOfFamily
                        ? '<span class="inline-flex items-center rounded-full bg-primary-100 px-2.5 py-0.5 text-xs font-medium text-primary-800">Head of Family</span>'
                        : '';
                    return "{$state}<br>{$badge}";
                })
                ->html(),
                Tables\Columns\TextColumn::make('date_of_birth')->sortable(),
                Tables\Columns\TextColumn::make('occupation')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('gender')->sortable(),
            ]);
    }
}