<?php

namespace App\Filament\Resources\PeroranganResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\CorporateResource; // <-- 1. Import CorporateResource

class CorporateRelationManager extends RelationManager
{
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->corporates()->exists();
    }
    protected static string $relationship = 'corporates';

    protected static ?string $title = 'Perusahaan Terkait';
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                Tables\Columns\TextColumn::make('nama'),
                Tables\Columns\TextColumn::make('level'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('telepon'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('goToCorporate')
                    ->label('Lihat Perusahaan')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('info') // Warna 'info' atau 'gray' lebih cocok untuk aksi 'lihat'
                    // 2. FIX: Gunakan getUrl() dari CorporateResource dan ID dari $record
                    ->url(fn($record): string => CorporateResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(), // atau hapus jika ingin redirect di tab yang sama
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Sebaiknya gunakan DetachBulkAction untuk relasi many-to-many
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
