<?php

namespace App\Filament\Resources;

use Filament\Forms;

use App\Models\Cell;
use App\Models\User;
use Filament\Tables;
use App\Models\Sector;
use App\Models\Village;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\District;
use App\Models\Province;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use App\Models\TransferRequest;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Collection;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ExportBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransferRequestResource\Pages;
use App\Filament\Resources\TransferRequestResource\RelationManagers;

class TransferRequestResource extends Resource
{
    protected static ?string $model = TransferRequest::class;
    protected static int $globalSearchResultsLimit = 20;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Services';
    protected static ?int $navigationSort = -1;



    // public static function getEloquentQuery(): Builder
    // {
    //     $query = parent::getEloquentQuery();

    //     return $query->where(function (Builder $query) {
    //         $user = auth()->user();

    //         $query->where('citizen_id', $user->id)
    //               ->orWhere('to_village_id', $user->village_id)
    //               ->orWhereHas('toVillage', function (Builder $query) use ($user) {
    //                   $query->where('id', $user->village_id);
    //               });

    //         if ($user->hasRole('pastor')) {
    //             $query->orWhereRaw('1 = 1');  // Allows pastors to see all requests
    //         }
    //     });
    // }

    
    

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        return $query->where(function (Builder $query) use ($user) {
            if ($user->hasRole('citizen')) {
                $query->where('citizen_id', $user->id);
            } elseif ($user->hasRole('pastor')) {
                $query->where('to_village_id', $user->village_id)
                      ->orWhere('from_village_id', $user->village_id);
            }
        });
    }


    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('citizen_id')
                    ->default(fn () => Auth::id()),
                
                Forms\Components\Hidden::make('from_village_id')
                    ->default(fn () => Auth::user()->village_id),

                Forms\Components\Select::make('province_id')
                    ->label('Province')
                    ->preload()
                    ->options(Province::all()->pluck('name', 'id'))
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                     $set('district_id',null);
                     $set('sector_id',null);
                     $set('cell_id',null);
                     $set('to_village_id',null);
                     })
                    ->required(),
                Forms\Components\Select::make('district_id')
                    ->label('District')
                    ->options(fn(Get $get): Collection => District::all()
                    ->where('province_id', $get('province_id'))
                    ->pluck('name','id'))
                    ->preload()
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                     $set('sector_id',null);
                     $set('cell_id',null);
                     $set('to_village_id',null);
                     })
                    ->required(),
                Forms\Components\Select::make('sector_id')
                    ->label('Sector')
                    ->options(fn(Get $get): Collection => Sector::all()
                    ->where('district_id', $get('district_id'))
                    ->pluck('name','id'))
                    ->preload()
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                     $set('cell_id',null);
                     $set('to_village_id',null);
                     }),
                Forms\Components\Select::make('cell_id')
                    ->label('Cell')
                    ->options(fn(Get $get): Collection => Cell::all()
                    ->where('sector_id', $get('sector_id'))
                    ->pluck('name','id'))
                    ->preload()
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('to_village_id',null)),
                Forms\Components\Select::make('to_village_id')
                    ->label('Village')
                    ->options(fn(Get $get): Collection => Village::all())
                        //->where('cell_id', $get('cell_id'))
                        ->options(Village::where('id', '!=', Auth::user()->village_id)->pluck('name', 'id'))
                    ->preload()
                    ->searchable()
                    ->required()
                    ->live(),
                Forms\Components\Textarea::make('reason'),
                // Forms\Components\Select::make('to_village_id')
                //     ->label('To Village')
                //     ->options(Village::where('id', '!=', Auth::user()->village_id)->pluck('name', 'id'))
                //     ->searchable()
                //     ->required(),
                
                
                Forms\Components\Hidden::make('approval_status')
                    ->default('pending'),
            ]);
    }


    public static function table(Table $table): Table
        {
            return $table
                ->columns([
                    Tables\Columns\TextColumn::make('citizen.name')
                        ->label('Citizen')
                        ->formatStateUsing(fn ($record) => $record->citizen->firstname . ' ' . $record->citizen->lastname)
                        ->searchable(['firstname', 'lastname'])
                        ->sortable(),
                    Tables\Columns\TextColumn::make('fromVillage.name')
                        ->label('From Village')
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('toVillage.name')
                        ->label('To Village')
                        ->searchable()
                        ->sortable(),
                        Tables\Columns\BadgeColumn::make('approval_status')
                        ->label('Status')
                        ->colors([
                            'primary' => 'Pending',
                            'success' => 'Approved',
                            'danger' => 'Rejected',
                        ]),
                    Tables\Columns\TextColumn::make('approvedBy')
                        ->label('Approved By')
                        ->sortable()
                        ->toggleable()
                        ->hidden(fn ($record) => !$record || $record->approved_by === null), 
                    Tables\Columns\TextColumn::make('toVillage.name')
                        ->label('To Village')
                        ->searchable()
                        ->sortable(),      
                    Tables\Columns\TextColumn::make('created_at')
                        ->date()
                        ->sortable()
                        ->label('Requested On'),
                    Tables\Columns\TextColumn::make('updated_at')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('deleted_at')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ])
                ->filters([
                    SelectFilter::make('approval_status')
                        ->options([
                            'Pending' => 'Pending',
                            'Approved' => 'Approved',
                            'Rejected' => 'Rejected',
                        ])
                        ->label('Status')
                        ->placeholder('All Statuses'),
                ])
                
                ->actions([
                    Action::make('approve')
                    ->label('Approve Request')
                    ->color('success')
                    ->icon('heroicon-s-check-circle')
                    ->action(function (TransferRequest $record) {
                        $record->approve();
                        // Get the user associated with this transfer request
                        $user = $record->citizen;

                        // Check if the user has the 'cov' role
                        if ($user->hasRole('cov')) {
                            // Remove the 'cov' role
                            $user->removeRole('cov');

                            // Add the 'citizen' role
                            $citizenRole = Role::where('name', 'citizen')->first();
                            $user->assignRole($citizenRole);

                            // Update the user's village_id
                            $user->village_id = $record->to_village_id;
                            $user->save();
                        }
                        Notification::make()
                            ->title('Transfer Request Approved')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (TransferRequest $record): bool => 
                        auth()->user()->hasAnyRole(['cov', 'super_admin']) && 
                        (auth()->user()->village_id === $record->to_village_id || auth()->user()->hasAnyRole(['cov','super_admin'])) &&
                        $record->approval_status !== 'Approved'
                    )
                    ->requiresConfirmation(),
                    Tables\Actions\DeleteAction::make()->visible(fn (TransferRequest $record): bool => auth()->user()->can('delete', $record)),
                    
                ])
                ->bulkActions([
                    Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                        ExportBulkAction::make(),
                    ]),
                ]);

    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTransferRequests::route('/'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['citizen_id'] = auth()->id();
        $data['from_village_id'] = auth()->user()->village_id;
        //$data['request_date'] = now();  // Ensure this is always set
        $data['approval_status'] = 'Pending';

        return $data;
    }

    public static function getNavigationBadge(): ?string
{
    return static::getModel()::count();
}

}
