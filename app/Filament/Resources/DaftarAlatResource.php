<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use App\Models\DaftarAlat;
use Filament\Tables\Table;
use App\Traits\GlobalForms;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use App\Filament\Resources\DaftarAlatResource\Pages;


class DaftarAlatResource extends Resource
{
    use GlobalForms;
    protected static ?string $model = DaftarAlat::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench';
    protected static ?string $navigationLabel = 'Daftar Alat';
    protected static ?string $navigationGroup = 'Manajemen Data Master';
    protected static ?string $pluralModelLabel = 'Daftar Alat';
    protected static ?int $navigationSort = 3;
    protected static ?int $navigationGroupSort = 1;

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
                Select::make('pemilik_id')
                    ->relationship('pemilik', 'nama')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Section::make('Informasi Pribadi')
                            ->schema([
                                TextInput::make('nama')
                                    ->label(label: 'Nama Pemilik (Sesuai KTP)')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('gender')
                                    ->dehydrated()
                                    ->label('Jenis Kelamin')
                                    ->options([
                                        'Pria' => 'Pria',
                                        'Wanita' => 'Wanita',
                                    ])
                                    ->required(),
                                TextInput::make('NIK')
                                    ->label('Nomor Induk Kependudukan (NIK)')
                                    ->string()
                                    ->unique()
                                    ->validationMessages([
                                        'unique' => 'NIK ini sudah terdaftar, silakan gunakan yang lain.',
                                    ])
                                    ->minLength(16)
                                    ->maxLength(16)
                                    ->required(),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->unique()
                                    ->validationMessages([
                                        'unique' => 'Email ini sudah terdaftar, silakan gunakan yang lain.',
                                    ])
                                    ->email()
                                    ->required(),
                                TextInput::make('telepon')
                                    ->label('Nomor Telepon')
                                    ->tel()
                                    ->required(),
                            ])->columns(2),

                        Section::make('Alamat')
                            ->schema(self::getAddressFields())->columns(2),
                    ])
                    ->preload()
                    ->required(),
                Textarea::make('keterangan')
                    ->nullable()
                    ->columnSpanFull(),

                Select::make('kondisi')
                    ->label('Kondisi Alat')
                    ->required()
                    ->options([
                        true => 'Baik',
                        false => 'Dipakai',
                    ])
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_seri')
                    ->searchable()
                    ->sortable()
                    ->label('Nomor Seri'),
                Tables\Columns\TextColumn::make('jenisAlat.nama')
                    ->searchable()
                    ->label('Jenis Alat')
                    ->sortable(),
                Tables\Columns\TextColumn::make('merk.nama')
                    ->searchable()
                    ->label('Merk Alat')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pemilik.nama')
                    ->searchable()
                    ->sortable()
                    ->label('Pemilik Alat'),

                BadgeColumn::make('kondisi')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Baik' : 'Bermasalah')
                    ->color(fn(bool $state): string => match ($state) {
                        true => 'success',
                        false => 'danger',
                    }),

                Tables\Columns\TextColumn::make('status_text')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Tersedia' => 'success',
                        'Dipakai' => 'warning',
                        'Terjual' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d-m-Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('jenis_alat')
                    ->options([
                        'GPS' => 'GPS',
                        'Drone' => 'Drone',
                        'OTS' => 'OTS',
                    ]),
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
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Alat Terdaftar')
            ->emptyStateDescription('Silahkan buat data alat baru untuk memulai.')
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListDaftarAlats::route('/'),
            'create' => Pages\CreateDaftarAlat::route('/create'),
            'edit' => Pages\EditDaftarAlat::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('kelola daftar alat'); // atau permission spesifik
    }
}
