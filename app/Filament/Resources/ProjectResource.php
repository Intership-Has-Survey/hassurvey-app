<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use App\Models\Project;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProjectResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProjectResource\RelationManagers;

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
            Forms\Components\Select::make('kategori_id')
                ->relationship('Kategori', 'nama')
                ->searchable()
                ->preload()
                ->label('Kategori Projek')
                ->required()
                ->createOptionForm([
                    Forms\Components\TextInput::make('nama')
                        ->label('Jenis Kategori')
                        ->required()
                        ->maxLength(50),
                ]),
            Forms\Components\Select::make('sumber')
                ->options([
                    'online' => 'online',
                    'offline' => 'offline',
                ])
                ->required()
                ->native(false),
            Forms\Components\Select::make('sales_id')
                ->relationship('Sales', 'nama')
                ->searchable()
                ->preload()
                ->label('Sales')
                ->required()
                ->createOptionForm([
                    Forms\Components\TextInput::make('nama')
                        ->label('Nama Sales')
                        ->required()
                        ->maxLength(50),
                    Forms\Components\TextInput::make('telepon')
                        ->label('Telepon')
                        ->required()
                        ->maxLength(50),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->required()
                        ->maxLength(50),
                ]),
            Forms\Components\TextInput::make('nama_klien'),
            Forms\Components\Select::make('jenis_penjualan')
                ->options([
                    'bussiness' => 'Bussiness',
                    'customer' => 'Customer',
                ])
                ->required()
                ->native(false)
                ->live(),
            Forms\Components\Select::make('level_company')
                ->options([
                    'besar' => 'Besar',
                    'kecil' => 'Kecil',
                ])
                ->native(false)
                ->visible(fn(Forms\Get $get) => $get('jenis_penjualan') === 'bussiness'),
            Forms\Components\TextInput::make('lokasi'),
            Forms\Components\TextInput::make('alamat'),
            Forms\Components\Select::make('status')
                ->options([
                    'prospect' => 'prospect',
                    'follow up' => 'follow up',
                    'closing' => 'closing',
                ])
                ->required()
                ->native(false),
            Forms\Components\TextInput::make('nilai_project'),
            Forms\Components\DatePicker::make('tanggal_informasi_masuk'),
            Forms\Components\TextInput::make('nama_pic'),
            Forms\Components\TextInput::make('nomor_wa_pic'),
            Forms\Components\TextInput::make('status_pekerjaan_lapangan')
                ->disabled()
                ->hint('Field ini tidak bisa diisi.')
                ->hintColor('gray'),
            Forms\Components\TextInput::make('status_pembayaran')->disabled()->hint('Field ini tidak bisa diisi.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_project')->sortable()->searchable(),
                TextColumn::make('kategori_id')->sortable()->searchable(),
                TextColumn::make('sumber')->sortable()->searchable(),
                TextColumn::make('sales_id')->sortable()->searchable(),
                TextColumn::make('nama_klien')->label('Klien')->sortable()->searchable(),
                TextColumn::make('lokasi')->sortable()->searchable(),
                TextColumn::make('alamat')->sortable()->searchable(),
                TextColumn::make('status')->sortable()->badge(),
                TextColumn::make('nilai_project')->label('Nilai')->sortable(),
                TextColumn::make('tanggal_informasi_masuk')->label('Masuk')->date(),
                TextColumn::make('nama_pic')->label('nama_pic'),
                TextColumn::make('wa_pic')->label('wa_pic'),
                TextColumn::make('status_pekerjaan_lapangan')->label('status_pekerjaan_lapangan'),
                TextColumn::make('status_pembayaran')->label('status_pembayaran'),
                TextColumn::make('created_at')->dateTime()->label('Dibuat'),
            ])
            ->filters([

                // DateRangeFilter::make('tanggal_informasi_masuk')
                //     ->label('Tanggal Masuk'),
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
