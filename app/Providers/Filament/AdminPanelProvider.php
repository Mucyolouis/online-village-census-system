<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Shield\RoleResource;
use Filament\PanelProvider;
use App\Settings\GeneralSettings;
use App\Filament\Pages\Auth\Login;
use App\Livewire\MyProfileExtended;
use Awcodes\Overlook\OverlookPlugin;
use App\Filament\Pages\Auth\Register;
use Illuminate\Support\Facades\Storage;
use App\Filament\Resources\RolesResource;
use Filament\Http\Middleware\Authenticate;
use Awcodes\Overlook\Widgets\OverlookWidget;
use App\Filament\Pages\Auth\EmailVerification;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Filament\Pages\Auth\RequestPasswordReset;
use Afsakar\FilamentOtpLogin\FilamentOtpLoginPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->spa()
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->registration(Register::class)
            ->passwordReset(RequestPasswordReset::class)
            ->emailVerification(EmailVerification::class)
            ->favicon(fn (GeneralSettings $settings) => Storage::url($settings->site_favicon))
            ->brandName('Online Village Census System')
            //->brandName(fn (GeneralSettings $settings) => $settings->brand_name)
            //->brandLogo(fn (GeneralSettings $settings) => Storage::url($settings->brand_logo))
            //->brandLogoHeight(fn (GeneralSettings $settings) => $settings->brand_logoHeight)
            ->colors(fn (GeneralSettings $settings) => $settings->site_theme)
            ->databaseNotifications()->databaseNotificationsPolling('30s')
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->sidebarCollapsibleOnDesktop()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->resources([
                config('filament-logger.activity_resource')
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                //OverlookWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentOtpLoginPlugin::make(),
                FilamentBackgroundsPlugin::make()
                    ->showAttribution(false)
                    ->imageProvider(
                        MyImages::make()
                            ->directory('\images\backgrounds')
                    ),
                \RickDBCN\FilamentEmail\FilamentEmail::make(),
                \BezhanSalleh\FilamentExceptions\FilamentExceptionsPlugin::make(),
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 2,
                        'sm' => 1
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
                \Jeffgreco13\FilamentBreezy\BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true,
                        shouldRegisterNavigation: false,
                        navigationGroup: 'Settings',
                        hasAvatars: true,
                        slug: 'my-profile'
                    )
                    ->myProfileComponents([
                        'personal_info' => MyProfileExtended::class,
                    ]),
            ]);
    }
}