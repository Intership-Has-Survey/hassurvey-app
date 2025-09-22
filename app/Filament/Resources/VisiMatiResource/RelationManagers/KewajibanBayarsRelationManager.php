<?php

namespace App\Filament\Resources\VisiMatiResource\RelationManagers;

use App\Filament\Resources\OperasionalResource\RelationManagers\KewajibanBayarsRelationManager as BaseKewajibanBayarsRelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

class KewajibanBayarsRelationManager extends BaseKewajibanBayarsRelationManager
{
    protected static string $relationship = 'kewajibanBayars';

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data): Model {
                        return $this->getOwnerRecord()->operasional->kewajibanBayars()->create($data);
                    }),
            ]);
    }
}