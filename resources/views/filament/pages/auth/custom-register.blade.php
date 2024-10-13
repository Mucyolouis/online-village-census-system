<x-filament-panels::page class="fi-page-auth-register">
    <div class="custom-register-container">
        <h2 class="mb-4 text-2xl font-bold">Register Citizen Details </h2>
        
        <x-filament-panels::form wire:submit="register">
            {{ $this->form }}

            <x-filament-panels::form.actions
                :actions="$this->getCachedFormActions()"
                :full-width="$this->hasFullWidthFormActions()"
            />
        </x-filament-panels::form>
    </div>
</x-filament-panels::page>