<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Pages\Actions;
use App\Models\StatusPekerjaan;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\BulkActionGroup;
use App\Filament\Resources\StatusPekerjaanResource\Pages;

class StatusPekerjaanResource extends Resource
{
    protected static ?string $model = StatusPekerjaan::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Status Pekerjaan';
    protected static ?string $navigationGroup = 'Jasa Pemetaan';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        $statusOptions = [
            'Belum Selesai' => 'Belum Selesai',
            'Selesai' => 'Selesai',
            'Tidak Perlu' => 'Tidak Perlu',
        ];

        return $form
            ->schema([
                Forms\Components\Select::make('project_id')
                    ->relationship('project', 'nama_project')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('pekerjaan_lapangan')->options($statusOptions)->required(),
                Forms\Components\Select::make('proses_data_dan_gambar')->options($statusOptions)->required(),
                Forms\Components\Select::make('laporan')->options($statusOptions)->required(),

                Forms\Components\Textarea::make('keterangan')->columnSpanFull(),
                Forms\Components\Hidden::make('user_id')->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project.nama_project')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('pekerjaan_lapangan')->badge(),
                Tables\Columns\TextColumn::make('proses_data_dan_gambar')->badge(),
                Tables\Columns\TextColumn::make('laporan')->badge(),
                Tables\Columns\TextColumn::make('user.name')->label('Dibuat Oleh'),
            ])
            ->filters([
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStatusPekerjaans::route('/'),
            'create' => Pages\CreateStatusPekerjaan::route('/create'),
            'edit' => Pages\EditStatusPekerjaan::route('/{record}/edit'),
        ];
    }
    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
