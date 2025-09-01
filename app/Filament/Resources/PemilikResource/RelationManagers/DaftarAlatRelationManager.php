<?php

namespace App\Filament\Resources\PemilikResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Actions\EditAction;
use Illuminate\Validation\Rules\Unique;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;


class DaftarAlatRelationManager extends RelationManager
{
    protected static string $relationship = 'DaftarAlat';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nomor_seri')
                    ->required()
                    ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule) {
                        $rule->where('company_id', Filament::getTenant()->id);
                        return $rule;
                    })
                    ->maxLength(255),
                Forms\Components\Select::make('jenis_alat_id')
                    ->relationship('jenisAlat', 'nama')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Jenis Alat')
                            ->required(),
                        Forms\Components\TextInput::make('keterangan')
                            ->label('Keterangan')
                            ->nullable(),
                    ])->validationMessages([
                        'required' => 'Jenis alat wajib dipilih.',
                    ])
                    ->required(),
                Forms\Components\Select::make('merk_id')
                    ->relationship('merk', 'nama')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Merk')
                            ->required(),
                    ])->required()
                    ->validationMessages([
                        'required' => 'Merk wajib dipilih.',
                    ]),

                Hidden::make('company_id')
                    ->default(fn() => $this->ownerRecord->company_id),
            ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nomor_seri')
            ->columns([
                Tables\Columns\TextColumn::make('nomor_seri'),
                Tables\Columns\TextColumn::make('jenisAlat.nama')
                    ->label('Jenis Alat'),
                Tables\Columns\TextColumn::make('merk.nama')
                    ->label('Merk Alat'),
                BadgeColumn::make('kondisi')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Baik' : 'Bermasalah')
                    ->color(fn(bool $state): string => match ($state) {
                        true => 'success',
                        false => 'danger',
                    }),
                BadgeColumn::make('status')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Tersedia' : 'Tidak Tersedia')
                    ->color(fn(bool $state): string => match ($state) {
                        true => 'success',
                        false => 'warning',
                    }),
            ])
            ->filters([
                SelectFilter::make('jenis_alat')
                    ->relationship('jenisAlat', 'nama')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('kondisi')
                    ->label('Kondisi')
                    ->placeholder('Semua Kondisi')
                    ->trueLabel('Baik')
                    ->falseLabel('Bermasalah'),

                TernaryFilter::make('status')
                    ->label('Ketersediaan')
                    ->placeholder('Semua Status')
                    ->trueLabel('Tersedia')
                    ->falseLabel('Tidak Tersedia'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
