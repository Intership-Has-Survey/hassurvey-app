<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StatusPekerjaanResource\Pages;
use App\Models\StatusPekerjaan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
                    ->unique(ignoreRecord: true), // Hanya boleh ada satu status per proyek

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
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
}
