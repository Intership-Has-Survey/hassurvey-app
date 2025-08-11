<?php

namespace App\Filament\Resources\KalibrasiResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Perorangan;
use App\Models\TrefRegion;
use Illuminate\Validation\Rule;
use Filament\Tables\Table;
use App\Traits\GlobalForms;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class DetailKalibrasiRelationManager extends RelationManager
{
    use GlobalForms;
    protected static string $relationship = 'alatCustomers';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('alat_customer_id')
                    ->label('Pilih Alat')
                    ->relationship('alatCustomer', 'nomor_seri')
                    ->searchable()
                    ->validationMessages([
                        'required' => 'Alat wajib dipilih',
                    ])
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\Select::make('jenis_alat_id')
                            ->relationship('jenisAlat', 'nama')
                            ->required()
                            ->searchable()
                            ->validationMessages([
                                'required' => 'Jenis alat wajib diisi',
                            ])
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nama')
                                    ->required(),
                            ]),
                        Forms\Components\Select::make('merk_id')
                            ->relationship('merk', 'nama')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->validationMessages([
                                'required' => 'Merk wajib diisi',
                            ])
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nama')
                                    ->unique(ignoreRecord: true)
                                    ->rules([
                                        Rule::unique('merk', 'nama')->whereNull('deleted_at'),
                                    ])
                                    ->validationMessages([
                                        'unique' => 'Nama merk ini sudah terdaftar.',
                                    ])
                                    ->required(),
                            ]),
                        Forms\Components\TextInput::make('nomor_seri')
                            ->required()
                            ->unique()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('keterangan')
                            ->nullable(),
                        Section::make('Informasi Pemilik Alat')
                            ->schema([
                                Select::make('customer_flow_type')
                                    ->label('Tipe Pemilik')
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
                                    ->relationship('perorangan', 'nama')
                                    ->searchable()
                                    ->createOptionForm(self::getPeroranganForm())
                                    ->createOptionUsing(fn(array $data): string => Perorangan::create($data)->id)
                                    ->visible(fn(Get $get) => $get('customer_flow_type') === 'perorangan')
                                    ->required(fn(Get $get) => $get('customer_flow_type') === 'perorangan'),

                                Hidden::make('company_id')
                                    ->default(fn() => \Filament\Facades\Filament::getTenant()?->getKey()),
                            ]),
                    ])
                    ->createOptionUsing(function (array $data): string {
                        return \App\Models\AlatCustomer::create($data)->id;
                    })
                    ->required(),
                Forms\Components\DatePicker::make('tgl_masuk')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->default(now())
                    ->visible('edit')
                    ->native(false),
                Forms\Components\DatePicker::make('tgl_stiker_kalibrasi')
                    ->label('Tanggal Stiker Kalibrasi')
                    ->default(now())
                    ->native(false)
                    ->visibleOn('edit'),
                Forms\Components\DatePicker::make('tgl_keluar')
                    ->visibleOn('edit')
                    ->label('Tanggal Keluar')
                    ->default(now())
                    ->native(false),
                Select::make('status')
                    ->visibleOn('edit')
                    ->options([
                        'belum_dikerjakan' => 'Belum dikerjakan',
                        'proses' => 'Dalam proses',
                        'kalibrasi_diluar' => 'Kalibrasi diluar HAS',
                        'sudah_diservis' => 'Sudah diservis',
                        'terkalibrasi' => 'Terkalibrasi'
                    ])
                    ->default('belum_dikerjakan')
                    ->native(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nomor_seri')
            ->columns([
                Tables\Columns\TextColumn::make('alatCustomer.nomor_seri')->label('nomor seri'),
                Tables\Columns\TextColumn::make('tgl_masuk'),
                Tables\Columns\TextColumn::make('tgl_stiker_kalibrasi')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('tgl_keluar')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'belum_dikerjakan' => 'gray',
                        'dalam_proses' => 'primary',
                        'kalibrasi_diluar' => 'warning',
                        'sudah_diservis' => 'info',
                        'terkalibrasi' => 'success',
                        default => 'primary'
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
