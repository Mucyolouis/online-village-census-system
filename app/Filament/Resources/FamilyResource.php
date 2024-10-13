<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Family;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\FamilyResource\Pages;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FamilyResource\RelationManagers;

class FamilyResource extends Resource
{
    protected static ?string $model = Family::class;
    protected static ?string $navigationGroup = 'Services';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('family_code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('village.name')
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('members_count')
                    ->label('Members')
                    ->counts('members')
                    ->color('success')
                    // ->tooltip(function (User $record): string {
                    //     $memberNames = $record->members->pluck('name')->join(', ');
                    //     return "Members: $memberNames";
                    // })
                    ->sortable(),
                TextColumn::make('headOfFamily.name')
                    ->label('Head of Family')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                // Add any filters you need
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                ViewAction::make('view_members')
                    ->label('View Members')
                    ->url(fn (Family $record): string => static::getUrl('view-members', ['record' => $record]))
                    ->slideOver()
                    ->icon('heroicon-o-users'),
                // ViewAction::make('view_members')
                //     ->label('View Members')
                //     ->modalHeading(fn (Family $record): string => "Members of {$record->family_code}")
                //     ->modalContent(fn (Family $record): Infolist => 
                //         static::getMembersInfolist($record)
                //     )
                //     ->modalWidth('7xl')
                //     ->slideOver()
                //     ->icon('heroicon-o-users'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFamilies::route('/'),
            'view-members' => Pages\ViewFamilyMembers::route('/{record}/members'),
            //'create' => Pages\CreateFamily::route('/create'),
            //'edit' => Pages\EditFamily::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    // public static function getMembersInfolist(Family $family): Infolist
    // {
    //     return Infolist::make()
    //         ->schema([
    //             RepeatableEntry::make('members')
    //                 ->schema([
    //                     TextEntry::make('name'),
    //                     TextEntry::make('date_of_birth'),
    //                     TextEntry::make('occupation'),
    //                     TextEntry::make('gender'),
    //                 ])
    //                 ->columns(4)
    //         ]);
    // }
}
