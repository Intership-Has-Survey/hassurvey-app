<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Kalibrasi;
use App\Models\Perorangan;
use Filament\Tables\Table;
use App\Traits\GlobalForms;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\KalibrasiResource\Pages;
use App\Filament\Resources\KalibrasiResource\RelationManagers\PengajuanDanasRelationManager;
use App\Filament\Resources\KalibrasiResource\RelationManagers\DetailKalibrasiRelationManager;
use App\Filament\Resources\KalibrasiResource\RelationManagers\StatusPembayaranRelationManager;

class KalibrasiResource extends Resource
{
    use GlobalForms;
    protected static ?string $model = Kalibrasi::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Kalibrasi';
    protected static ?string $navigationGroup = 'Layanan';
    protected static ?string $pluralModelLabel = 'Jasa Kalibrasi';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Customer')
                    ->schema([
                        Select::make('customer_flow_type')
                            ->label('Tipe Customer')
                            ->options([
                                'perorangan' => 'Perorangan',
                                'corporate' => 'Corporate'
                            ])
                            ->live()
                            ->required()
                            ->dehydrated(false)
                            ->afterStateUpdated(function (Set $set) {
                                $set('corporate_id', null);
                                $set('perorangan_id', null);
                            }),

                        // Jika corporate
                        Select::make('corporate_id')
                            ->label('Pilih Perusahaan')
                            ->relationship('corporate', 'nama')
                            ->searchable()
                            ->preload()
                            ->createOptionForm(self::getCorporateForm())
                            ->visible(fn(Get $get) => $get('customer_flow_type') === 'corporate')
                            ->required(fn(Get $get) => $get('customer_flow_type') === 'corporate'),

                        // Jika perorangan
                        Select::make('perorangan_id')
                            ->label('Pilih Customer')
                            ->relationship('perorangan', 'nama')
                            ->searchable()
                            ->preload()
                            ->createOptionForm(self::getPeroranganForm())
                            ->createOptionUsing(fn(array $data): string => Perorangan::create($data)->id)
                            ->visible(fn(Get $get) => $get('customer_flow_type') === 'perorangan')
                            ->required(fn(Get $get) => $get('customer_flow_type') === 'perorangan'),
                    ]),
                Section::make('Informasi Kalibrasi')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Kalibrasi')
                            ->required(),
                        TextInput::make('harga')
                            ->label('Harga')
                            ->numeric()
                            ->prefix('Rp ')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(','),
                        Select::make('status')
                            ->options([
                                'dalam_proses' => 'Dalam proses',
                                'selesai' => 'Selesai'
                            ])
                            ->visibleOn('edit')
                            ->default('dalam_proses')
                            ->native(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('nama'),
                Tables\Columns\TextColumn::make('corporate_id')
                    ->label('Customer')
                    ->getStateUsing(function ($record) {
                        return optional($record->corporate)->nama
                            ?? optional($record->perorangan)->nama
                            ?? 'Tidak ada customer';
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'dalam_proses' => 'primary',
                        'selesai' => 'success',
                        default => 'primary'
                    }),
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
            ])
            ->defaultSort('created_at', 'desc');;
    }

    public static function getRelations(): array
    {
        return [
            PengajuanDanasRelationManager::class,
            DetailKalibrasiRelationManager::class,
            StatusPembayaranRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKalibrasis::route('/'),
            'create' => Pages\CreateKalibrasi::route('/create'),
            'edit' => Pages\EditKalibrasi::route('/{record}/edit'),
        ];
    }
}
