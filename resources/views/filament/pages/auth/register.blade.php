<x-filament-panels::page.simple>
    <x-slot name="heading">
        {{-- {{ __('filament-panels::pages/auth/register.title') }} --}}
        <h2>Register Citizen Details </h2>
    </x-slot>

    <div class="w-full mx-auto max-w-8xl"> <!-- Increased max width -->
        <x-filament-panels::form wire:submit="register">
            {{ $this->form }}

            <x-filament-panels::form.actions
                :actions="$this->getFormActions()"
                :full-width="false"
            />
        </x-filament-panels::form>
    </div>
</x-filament-panels::page.simple>