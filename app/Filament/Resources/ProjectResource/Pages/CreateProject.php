<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Actions;
use App\Models\Corporate;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ProjectResource;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (filled($data['corporate_id'])) {
            $data['customer_flow_type'] = 'corporate';
        } else {
            $data['customer_flow_type'] = 'perorangan';
        }

        return $data;
    }
    protected function afterCreate(): void
    {
        $this->syncPicsToCorporate();
    }

    protected function syncPicsToCorporate(): void
    {
        $project = $this->getRecord();
        $data = $this->form->getState();

        if (($data['customer_flow_type'] ?? null) === 'corporate' && !empty($data['corporate_id'])) {
            $corporate = Corporate::find($data['corporate_id']);
            $picIds = $project->perorangan()->pluck('id');

            if ($corporate && $picIds->isNotEmpty()) {
                $corporate->perorangan()->syncWithoutDetaching($picIds);
            }
        }
    }
}
