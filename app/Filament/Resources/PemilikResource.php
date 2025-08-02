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
use Filament\Forms\Components\Hidden;
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

    public static function calculateAndSetTotals(Set $set, ?Model $record): void
    {
        if (!$record) {
            $set('total_pendapatanktr', 0);
            $set('total_pendapataninv', 0);
            $set('total_pendapatanhas', 0);

            $set('total_pendapatanktr_display', 0);
            $set('total_pendapataninv_display', 0);
            $set('total_pendapatanhas_display', 0);
            return;
        }

        $totalKotor = $record->riwayatSewaAlat()->sum('biaya_sewa_alat_final');
        $totalInvestor = $record->riwayatSewaAlat()->sum('pendapataninv_final');
        $totalHas = $record->riwayatSewaAlat()->sum('pendapatanhas_final');

        $set('total_pendapatanktr', $totalKotor);
        $set('total_pendapataninv', $totalInvestor);
        $set('total_pendapatanhas', $totalHas);

        $set('total_pendapatanktr_display', Number::currency($totalKotor, 'IDR'));
        $set('total_pendapataninv_display', Number::currency($totalInvestor, 'IDR'));
        $set('total_pendapatanhas_display', Number::currency($totalHas, 'IDR'));
    }

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
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'unique' => 'NIK ini sudah terdaftar, silakan gunakan yang lain.',
                            ])
                            ->length(16)
                            ->required(),
                        TextInput::make('email')
                            ->label('Email')
                            ->unique(ignoreRecord: true)
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
