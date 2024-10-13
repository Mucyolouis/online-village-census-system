<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;

class Approvals extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static string $view = 'filament.pages.approvals';

    protected static ?string $navigationGroup = 'Services';
    protected static ?string $getNavigationLabel = 'Approvals';

    protected static ?int $navigationSort = 2;

    public static function shouldRegister(): bool
    {
        $user = auth()->user();
        return $user && $user->is_approved && $user->hasAnyRole(['cov', 'super_admin']);
    }

    public function mount(): void
    {
        $user = auth()->user();
        if (!$user || !$user->is_approved || !$user->hasAnyRole(['cov', 'super_admin'])) {
            // Filament::notify('error', 'You do not have permission to access this page.');
            //redirect();
        }
    }

    

    public function table(Table $table): Table
    {
        return $table
            ->query(User::where('is_approved', 0))
            ->columns([
                TextColumn::make('username')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('firstname')->searchable(),
                TextColumn::make('lastname')->searchable(),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve Citizen')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn (User $record) => $record->approve())
                    ->visible(fn (User $record): bool => 
                        auth()->user()->hasAnyRole(['cov','super_admin']) && !$record->is_approved
                    )
                    ->requiresConfirmation(),
            ]);
    }
}