<?php

namespace App\Filament\Resources\VisiMatiResource\RelationManagers;

use App\Filament\Resources\TabunganResource\RelationManagers\PemasukansRelationManager as BasePemasukansRelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

class PemasukansRelationManager extends BasePemasukansRelationManager
{
    protected static string $relationship = 'pemasukans';

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data): Model {
                        return $this->ownerRecord->tabungan->pemasukans()->create($data);
                    }),
            ]);
    }
}
