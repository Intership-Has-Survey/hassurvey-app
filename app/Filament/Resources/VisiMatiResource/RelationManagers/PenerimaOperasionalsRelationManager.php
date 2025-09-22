<?php

namespace App\Filament\Resources\VisiMatiResource\RelationManagers;

use App\Filament\Resources\OperasionalResource\RelationManagers\PenerimaOperasionalsRelationManager as BasePenerimaOperasionalsRelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

class PenerimaOperasionalsRelationManager extends BasePenerimaOperasionalsRelationManager
{
    protected static string $relationship = 'penerimaOperasionals';

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data): Model {
                        return $this->getOwnerRecord()->operasional->penerimaOperasionals()->create($data);
                    }),
            ]);
    }
}