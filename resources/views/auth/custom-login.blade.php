<x-filament-panels::page.simple>
    <div class="custom-login-container">
        <div class="custom-login-card">
            <h2 class="custom-login-title">{{ __('filament::login.heading') }}</h2>

            <x-filament-panels::form wire:submit="authenticate">
                {{ $this->form }}

                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()"
                />
            </x-filament-panels::form>
        </div>
    </div>
</x-filament-panels::page.simple>