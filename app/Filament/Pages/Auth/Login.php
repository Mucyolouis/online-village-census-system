<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BasePage;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Css;

class Login extends BasePage
{
    protected static string $view = 'filament.pages.auth.custom-login';

    public function mount(): void
    {
        parent::mount();

        $this->form->fill([
            'email' => 'superadmin@admin.com',
            'password' => 'superadmin',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent()->label('Email'),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ]);
    }

    public function getHeading(): string | Htmlable
    {
        return '';
    }

    public static function getStyles(): array
    {
        return [
            ...parent::getStyles(),
            Css::make('custom-login', __DIR__ . '/../../../../resources/css/custom-login.css')->afterBundling()
        ];
    }
}