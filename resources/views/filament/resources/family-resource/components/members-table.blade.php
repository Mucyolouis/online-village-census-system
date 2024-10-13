<x-filament::table>
    <x-slot name="header">
        <x-filament::table.heading>Name</x-filament::table.heading>
        <x-filament::table.heading>Age</x-filament::table.heading>
        <x-filament::table.heading>Occupation</x-filament::table.heading>
        <x-filament::table.heading>Gender</x-filament::table.heading>
    </x-slot>

    @foreach ($family->members as $member)
        <x-filament::table.row>
            <x-filament::table.cell>{{ $member->name }}</x-filament::table.cell>
            <x-filament::table.cell>{{ $member->age }}</x-filament::table.cell>
            <x-filament::table.cell>{{ $member->occupation }}</x-filament::table.cell>
            <x-filament::table.cell>{{ $member->gender }}</x-filament::table.cell>
        </x-filament::table.row>
    @endforeach
</x-filament::table>