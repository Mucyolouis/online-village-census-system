<?php

namespace App\Filament\Resources;

use Closure;
use Exception;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Settings\MailSettings;
use Filament\Facades\Filament;
use Tables\Columns\BadgeColumn;
use Filament\Resources\Resource;
use Tables\Filters\SelectFilter;
use App\Filament\Filters\AgeFilter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\UserResource\Pages;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\TextFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\NumberFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\BooleanFilter;
use App\Filament\Resources\UserResource\Widgets\UserDemographicsWidget;

class UserResource extends Resource
{
    //protected static ?string $model = User::class;
    protected static int $globalSearchResultsLimit = 20;

    protected static ?string $model = User::class;
    protected static ?int $navigationSort = -1;
    protected static ?string $navigationIcon = 'heroicon-s-users';
    protected static ?string $navigationGroup = 'Access';
    protected static ?string $navigationLabel = 'Citizens';

    
    public static function getNavigationBadge(): ?string
{
    return static::getModel()::count();
}

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\Grid::make()
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('media')
                                ->hiddenLabel()
                                ->avatar()
                                ->collection('avatars')
                                ->alignCenter()
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('username')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('email')
                                ->email()
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('firstname')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('lastname')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Select::make('marital_status')
                                ->label('Marital Status')
                                ->options([
                                    'single' => 'Single',
                                    'married' => 'Married',
                                    'divorced' => 'Divorced',
                                    'widowed' => 'Widowed',
                                ])
                                ->required(),
                            ]),
                ])
                ->columnSpan([
                    'sm' => 1,
                    'lg' => 2
                ]),
            Forms\Components\Group::make()
                ->schema([
                    Forms\Components\Section::make('Role')
                        ->schema([
                            Select::make('roles')->label('Role')
                                ->hiddenLabel()
                                ->relationship('roles', 'name')
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => Str::headline($record->name))
                                ->multiple()
                                ->preload()
                                ->maxItems(1)
                                ->native(false),
                        ])
                        ->compact(),
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\TextInput::make('password')
                                ->password()
                                ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                                ->dehydrated(fn (?string $state): bool => filled($state))
                                ->revealable()
                                ->required(),
                            Forms\Components\TextInput::make('passwordConfirmation')
                                ->password()
                                ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                                ->dehydrated(fn (?string $state): bool => filled($state))
                                ->revealable()
                                ->same('password')
                                ->required(),
                        ])
                        ->compact()
                        ->hidden(fn (string $operation): bool => $operation === 'edit'),
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\Placeholder::make('email_verified_at')
                                ->label(__('resource.general.email_verified_at'))
                                ->content(fn (User $record): ?string => $record->email_verified_at),
                            Forms\Components\Actions::make([
                                Action::make('resend_verification')
                                    ->label(__('resource.user.actions.resend_verification'))
                                    ->color('secondary')
                                    ->action(fn (MailSettings $settings, Model $record) => static::doResendEmailVerification($settings, $record)),
                            ])
                            ->hidden(fn (User $user) => $user->email_verified_at != null)
                            ->fullWidth(),
                            Forms\Components\Placeholder::make('created_at')
                                ->label(__('resource.general.created_at'))
                                ->content(fn (User $record): ?string => $record->created_at?->diffForHumans()),
                            Forms\Components\Placeholder::make('updated_at')
                                ->label(__('resource.general.updated_at'))
                                ->content(fn (User $record): ?string => $record->updated_at?->diffForHumans()),
                        ])
                        ->hidden(fn (string $operation): bool => $operation === 'create'),
                ])
                ->columnSpan(1),
        ])
        ->columns(3);
}

    public static function table(Table $table): Table
    {
        return $table
            //->query(fn (Builder $query) => $query->scopeVisibleToUser(auth()->user()))
            ->columns([
                SpatieMediaLibraryImageColumn::make('media')->label('Avatar')
                    ->collection('avatars')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('username')->label('Username')
                    ->description(fn (Model $record) => $record->firstname.' '.$record->lastname)
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->formatStateUsing(function ($state, Model $record) {
                        if ($record->roles->isEmpty()) {
                            return 'Unverified';
                        }
                        return Str::headline($state);
                    })
                    ->colors([
                        'danger' => 'Unverified',
                        'info' => fn ($state) => $state !== 'Unverified',
                    ])
                    ->badge()
                    ->sortable(),
                    Tables\Columns\TextColumn::make('village.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('is_approved')
                    ->label('Approval Status')
                    ->colors([
                        'success' => 1,
                        'warning' => 0,
                    ])
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable()
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gender')
                    ->searchable()
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('national_ID')
                    ->searchable()
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->searchable()
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('age')
                    ->label('Age')
                    ->state(function (User $record): ?string {
                        if (!$record->date_of_birth) {
                            return null;
                        }
                        return $record->date_of_birth->age . ' years';
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('occupation.name')
                    ->label('Profession')
                    ->searchable()
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nationality.name')
                    ->searchable()
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('insurance')
                    ->searchable()
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('marital_status')
                    ->searchable()
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('religion')
                    ->searchable()
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('email_verified_at')->label('Verified at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\BooleanColumn::make('is_approved')
                    ->label('Approved')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Approved At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Disability filter
                Tables\Filters\SelectFilter::make('disability')
                    ->options(function () {
                        return User::distinct()
                            ->orderBy('disability')
                            ->pluck('disability', 'disability')
                            ->toArray();
                    })
                    ->preload()
                    ->searchable()
                    ->multiple(),

                // Education level filter
                Tables\Filters\SelectFilter::make('education_level')
                    ->options(function () {
                        return User::distinct()
                            ->orderBy('education_level')
                            ->pluck('education_level', 'education_level')
                            ->toArray();
                    })
                    ->preload()
                    ->searchable()
                    ->multiple(),
                // Religion filter
                Tables\Filters\SelectFilter::make('religion')
                    ->options(function () {
                        return User::distinct()
                            ->orderBy('religion')
                            ->pluck('religion', 'religion')
                            ->toArray();
                    })
                    ->preload()
                    ->searchable()
                    ->multiple(),
                //nationality filter
                Tables\Filters\SelectFilter::make('nationality')
                    ->label('Nationality')
                    ->options(function () {
                        return User::distinct()
                            ->orderBy('nationality')
                            ->pluck('nationality', 'nationality')
                            ->toArray();
                    })
                    ->preload()
                    ->searchable()
                    ->multiple(),
                //gender filter
                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'Male' => 'Male',
                        'Female' => 'Female',
                    ])
                    ->label('Gender')
                    ->placeholder('All Genders'),
                Tables\Filters\Filter::make('min_age')
                    ->form([
                        Forms\Components\TextInput::make('min_age')
                            ->label('Minimum Age')
                            ->numeric()
                            ->rules(['integer', 'min:0'])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_age'],
                                function (Builder $query, $minAge) {
                                    // Calculate the cutoff date for the given minimum age
                                    $cutoffDate = now()->subYears($minAge)->format('Y-m-d');
                                    
                                    // Filter users whose date_of_birth is before or on the cutoff date
                                    return $query->where('date_of_birth', '<=', $cutoffDate);
                                }
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        return $data['min_age'] ? "Minimum age: {$data['min_age']}" : null;
                    }),
                    Tables\Filters\Filter::make('max_age')
                    ->form([
                        Forms\Components\TextInput::make('max_age')
                            ->label('Maximum Age')
                            ->numeric()
                            ->rules(['integer', 'min:0'])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['max_age'],
                                function (Builder $query, $maxAge) {
                                    // Calculate the cutoff date for the given minimum age
                                    $cutoffDate = now()->subYears($maxAge)->format('Y-m-d');
                                    
                                    // Filter users whose date_of_birth is before or on the cutoff date
                                    return $query->where('date_of_birth', '>=', $cutoffDate);
                                }
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        return $data['max_age'] ? "Maximum age: {$data['max_age']}" : null;
                    }),
                Tables\Filters\SelectFilter::make('occupation')
                    ->label('Occupation')
                    ->options(function () {
                        return User::distinct()
                            ->orderBy('occupation')
                            ->pluck('occupation', 'occupation')
                            ->toArray();
                    })
                    ->multiple()
                    ->preload(),

                // NumberFilter::make('age'),
                // DateFilter::make('created_at'),
                DateFilter::make('date_of_birth'),
                
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve Citizen')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn (Model $record) => $record->approve())
                    ->visible(fn (Model $record): bool => 
                        auth()->user()->hasAnyRole(['cov','super_admin']) && !$record->is_approved
                    )
                    ->requiresConfirmation(),
                Impersonate::make(), 
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make(),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return $record->email;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['email', 'firstname', 'lastname'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'name' => $record->firstname.' '.$record->lastname,
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __("menu.nav_group.access");
    }

    public static function doResendEmailVerification($settings = null, $user): void
    {
        if (! method_exists($user, 'notify')) {
            $userClass = $user::class;

            throw new Exception("Model [{$userClass}] does not have a [notify()] method.");
        }

        $notification = new VerifyEmail();
        $notification->url = Filament::getVerifyEmailUrl($user);

        $settings->loadMailSettingsToConfig();

        $user->notify($notification);

        Notification::make()
            ->title(__('resource.user.notifications.notification_resent.title'))
            ->success()
            ->send();
    }


    protected function getTableQuery(): Builder
    {
        return static::getEloquentQuery();
    } 
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        Log::info('UserResource getEloquentQuery called');
        Log::info('Authenticated user ID: ' . $user->id);
        Log::info('User roles: ' . implode(', ', $user->roles->pluck('name')->toArray()));
        Log::info('User permissions: ' . implode(', ', $user->getAllPermissions()->pluck('name')->toArray()));
        Log::info('Authenticated user village_id: ' . $user->village_id);

        if ($user->hasRole('cov')) {
            Log::info('User has COV role, applying village filter');
            $query->where('village_id', $user->village_id);
        } elseif ($user->hasAnyRole(['super_admin', 'coc'])) {
            Log::info('User has super_admin or coc role, no filter applied');
        } else {
            Log::info('User has no special roles, filtering to show only their own record');
            $query->where('id', $user->id);
        }

        Log::info('Final SQL Query: ' . $query->toSql());
        Log::info('SQL Bindings: ' . json_encode($query->getBindings()));

        return $query;
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        Log::info('Checking navigation registration for user: ' . $user->id);
        Log::info('User roles: ' . implode(', ', $user->roles->pluck('name')->toArray()));
        Log::info('User permissions: ' . implode(', ', $user->permissions->pluck('name')->toArray()));
        return true; // or your original logic
    }
}
