<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Project';
    protected static ?string $navigationGroup = 'Jasa Pemetaan';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama_project')->required(),
            Forms\Components\TextInput::make('kategori')->required(),
            Forms\Components\TextInput::make('sumber'),
            Forms\Components\TextInput::make('sales'),
            Forms\Components\TextInput::make('nama_klien'),
            Forms\Components\TextInput::make('jenis_penjualan'),
            Forms\Components\TextInput::make('level_company'),
            Forms\Components\TextInput::make('lokasi'),
            Forms\Components\TextInput::make('alamat'),
            Forms\Components\TextInput::make('status'),
            Forms\Components\TextInput::make('nilai_project'),
            Forms\Components\DatePicker::make('tanggal_informasi_masuk'),
            Forms\Components\TextInput::make('nama_pic'),
            Forms\Components\TextInput::make('nomor_wa_pic'),
            Forms\Components\TextInput::make('status_pekerjaan_lapangan'),
            Forms\Components\TextInput::make('status_pembayaran'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_project')->sortable()->searchable(),
                TextColumn::make('kategori')->sortable()->searchable(),
                TextColumn::make('sales')->sortable()->searchable(),
                TextColumn::make('nama_klien')->label('Klien')->sortable()->searchable(),
                TextColumn::make('lokasi')->sortable()->searchable(),
                TextColumn::make('status')->sortable()->badge(),
                TextColumn::make('nilai_project')->label('Nilai')->sortable(),
                TextColumn::make('tanggal_informasi_masuk')->label('Masuk')->date(),
                TextColumn::make('created_at')->dateTime()->label('Dibuat'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
