<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Actions;
use App\Models\Corporate;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProjectResource;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (filled($data['corporate_id'])) {
            $data['customer_flow_type'] = 'corporate';
        } else {
            $data['customer_flow_type'] = 'perorangan';
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (filled($data['corporate_id'])) {
            $data['customer_flow_type'] = 'corporate';
        } else {
            $data['customer_flow_type'] = 'perorangan';
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $project = $this->getRecord();
        $data = $this->form->getState();

        if (($data['customer_flow_type'] ?? null) === 'corporate' && !empty($data['corporate_id'])) {
            $corporate = Corporate::find($data['corporate_id']);
            if ($corporate && !empty($data['perorangan'])) {
                $picIds = collect($data['perorangan'])->pluck('perorangan_id')->filter();
                $corporate->perorangans()->syncWithoutDetaching($picIds);
            }
        }
    }
}
