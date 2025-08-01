<?php

namespace App\Filament\Resources;

use App\Models\Pemilik;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Traits\GlobalForms;
use Illuminate\Support\Number;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\PemilikResource\Pages\EditPemilik;
use App\Filament\Resources\PemilikResource\Pages\ListPemiliks;
use App\Filament\Resources\PemilikResource\Pages\CreatePemilik;
use App\Filament\Resources\PemilikResource\RelationManagers\DaftarAlatRelationManager;
use App\Filament\Resources\PemilikResource\RelationManagers\RiwayatSewaPemilikRelationManager;

class PemilikResource extends Resource
{
    use GlobalForms;
    protected static ?string $model = Pemilik::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Pemilik/Investor Alat';
    protected static ?string $navigationGroup = 'Manajemen Data Master';

    protected static ?string $pluralModelLabel = 'Pemilik Alat';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Pribadi')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Pemilik (Sesuai KTP)')
                            ->required()
                            ->maxLength(255),
                        Select::make('gender')
                            ->options(['Pria' => 'Pria', 'Wanita' => 'Wanita'])
                            ->label('Jenis Kelamin')
                            ->required(),
                        TextInput::make('NIK')
                            ->label('Nomor Induk Kependudukan (NIK)')
                            ->unique()
                            ->validationMessages([
                                'unique' => 'NIK ini sudah terdaftar, silakan gunakan yang lain.',
                            ])
                            ->length(16)
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

                Section::make('Informasi Pendapatan & Bagi Hasil')
                    ->schema([
                        TextInput::make('persen_bagihasil')
                            ->label('Persentase Bagi Hasil (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(20)
                            ->postfix('%')
                            ->required(),

                        Placeholder::make('total_pendapatanktr')
                            ->label('Total Pendapatan Kotor')
                            ->content(function (?Model $record): string {
                                if (!$record)
                                    return 'Rp 0';
                                $total = $record->riwayatSewaAlat()->sum('biaya_sewa_alat');
                                return Number::currency($total, 'IDR');
                            }),

                        Placeholder::make('total_pendapataninv')
                            ->label('Total Pendapatan Investor/Pemilik')
                            ->content(function (?Model $record): string {
                                if (!$record)
                                    return 'Rp 0';
                                $total = $record->riwayatSewaAlat()->sum('pendapataninv');
                                return Number::currency($total, 'IDR');
                            }),

                        Placeholder::make('total_pendapatanhas')
                            ->label('Total Pendapatan untuk Has Survey')
                            ->content(function (?Model $record): string {
                                if (!$record)
                                    return 'Rp 0';
                                $total = $record->riwayatSewaAlat()->sum('pendapatanhas');
                                return Number::currency($total, 'IDR');
                            }),

                    ])->columns(1)
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')->label('Nama Pemilik')->searchable()->sortable(),
                TextColumn::make('NIK')->label('NIK')->searchable(),
                TextColumn::make('telepon')->label('No. Telepon')->searchable(),
                TextColumn::make('created_at')->dateTime()->label('Tanggal Dibuat')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Pemilik/Investor Alat yang Terdaftar')
            ->emptyStateDescription('Silahkan buat data pemilik/investor baru untuk memulai.')
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            DaftarAlatRelationManager::class,
            RiwayatSewaPemilikRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPemiliks::route('/'),
            'create' => CreatePemilik::route('/create'),
            'edit' => EditPemilik::route('/{record}/edit'),
        ];
    }
}
