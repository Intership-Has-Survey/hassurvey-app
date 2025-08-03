<?php

namespace App\Filament\Resources;

use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Perorangan;
use Filament\Tables\Table;
use App\Traits\GlobalForms;
use App\Models\AlatCustomer;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\AlatCustomerResource\Pages\EditAlatCustomer;
use App\Filament\Resources\AlatCustomerResource\Pages\ListAlatCustomers;
use App\Filament\Resources\AlatCustomerResource\Pages\CreateAlatCustomer;
use App\Filament\Resources\AlatCustomerResource\RelationManagers\DetailKalibrasiRelationManager;

class AlatCustomerResource extends Resource
{
    use GlobalForms;
    protected static ?string $model = AlatCustomer::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Alat Customer';
    protected static ?string $navigationGroup = 'Customer';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('jenis_alat_id')
                    ->relationship('jenisAlat', 'nama')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('nama')
                            ->label('Nama Jenis Alat')
                            ->required(),
                        TextInput::make('keterangan')
                            ->label('Keterangan')
                            ->nullable(),
                    ]),
                TextInput::make('nomor_seri')
                    ->required()
                    ->unique()
                    ->maxLength(255)
                    ->validationMessages([
                        'unique' => 'Nomor seri ini sudah terdaftar, silakan gunakan yang lain.',
                    ])
                    ->required(),
                Select::make('merk_id')
                    ->relationship('merk', 'nama')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('nama')
                            ->label('Nama Merk')
                            ->required(),
                    ])
                    ->required(),

                Select::make('kondisi')
                    ->label('Kondisi Alat')
                    ->required()
                    ->options([
                        true => 'Baik',
                        false => 'Dipakai',
                    ])
                    ->visibleOn('edit'),
                Textarea::make('keterangan')
                    ->nullable()
                    ->columnSpanFull(),
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
                            ->dehydrated(false) // karena ini bukan field database
                            ->afterStateUpdated(function (Set $set) {
                                $set('corporate_id', null);
                                $set('perorangan_id', null);
                            }),

                        // Jika corporate
                        Select::make('corporate_id')
                            ->label('Pilih Perusahaan')
                            ->relationship('corporate', 'nama')
                            ->createOptionForm(self::getCorporateForm())
                            ->visible(fn(Get $get) => $get('customer_flow_type') === 'corporate')
                            ->required(fn(Get $get) => $get('customer_flow_type') === 'corporate'),

                        // Jika perorangan
                        Select::make('perorangan_id')
                            ->label('Pilih Customer')
                            ->options(function (Get $get) {
                                if ($get('customer_flow_type') !== 'perorangan') {
                                    return [];
                                }
                                return Perorangan::all()->mapWithKeys(fn($p) => [$p->id => "{$p->nama} - {$p->nik}"])->all();
                            })
                            ->searchable()
                            ->createOptionForm(self::getPeroranganForm())
                            ->createOptionUsing(fn(array $data): string => Perorangan::create($data)->id)
                            ->visible(fn(Get $get) => $get('customer_flow_type') === 'perorangan')
                            ->required(fn(Get $get) => $get('customer_flow_type') === 'perorangan'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('corporate_id')
                    ->label('Customer')
                    ->getStateUsing(function ($record) {
                        return optional($record->corporate)->nama
                            ?? optional($record->perorangan)->nama
                            ?? 'Tidak ada customer';
                    }),
                TextColumn::make('jenisalat.nama')->label('Jenis Alat'),
                TextColumn::make('merk.nama')->label('Merek'),
                TextColumn::make('nomor_seri'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            DetailKalibrasiRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAlatCustomers::route('/'),
            'create' => CreateAlatCustomer::route('/create'),
            'edit' => EditAlatCustomer::route('/{record}/edit'),
        ];
    }
}
