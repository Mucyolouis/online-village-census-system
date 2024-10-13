<?php

namespace App\Filament\Resources\TransferRequestResource\Pages;

use App\Filament\Resources\TransferRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTransferRequests extends ManageRecords
{
    protected static string $resource = TransferRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->createAnother(false)  ,
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Remove ability to edit these fields
        unset($data['citizen_id'], $data['from_village_id'], $data['request_date'], $data['approval_status']);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure these fields aren't changed during edit
        $data['citizen_id'] = $this->record->citizen_id;
        $data['from_village_id'] = $this->record->from_village_id;
        //$data['request_date'] = $this->record->request_date;
        $data['approval_status'] = $this->record->approval_status;

        return $data;
    }

}
