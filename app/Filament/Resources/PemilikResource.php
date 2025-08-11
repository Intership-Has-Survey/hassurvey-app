<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Pemilik;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Traits\GlobalForms;
use Filament\Pages\Actions;
use Filament\Facades\Filament;
use Illuminate\Support\Number;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PemilikResource\Pages\EditPemilik;
use App\Filament\Resources\PemilikResource\Pages\ListPemiliks;
use App\Filament\Resources\PemilikResource\Pages\CreatePemilik;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;
use App\Filament\Resources\PemilikResource\RelationManagers\DaftarAlatRelationManager;
use App\Filament\Resources\PemilikResource\RelationManagers\RiwayatSewaPemilikRelationManager;
use App\Filament\Resources\PemilikResource\RelationManagers\TransaksiPembayaransRelationManager;

class PemilikResource extends Resource
{
    use GlobalForms;
    protected static ?string $model = Pemilik::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
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

        $uuid = request()->segment(2);
        return $form
            ->schema([
                Section::make('Informasi Pribadi')
                    ->schema([
                        TextInput::make('nik')
                            ->label('Nomor Induk Kependudukan (NIK)')
                            ->required()
                            ->length(16)
                            ->rule('regex:/^\d+$/')
                            ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule) {
                                $rule->where('company_id', Filament::getTenant()->id);
                                return $rule;
                            })
                            ->validationMessages([
                                'required' => 'NIK tidak boleh kosong',
                                'unique' => 'NIK sudah pernah terdaftar',
                                'regex' => 'NIK hanya boleh berisi angka',
                            ]),
                        TextInput::make('nama')
                            ->label('Nama Lengkap (Sesuai KTP)')
                            ->required()
                            ->maxLength(255),
                        Select::make('gender')
                            ->options(['Pria' => 'Pria', 'Wanita' => 'Wanita'])
                            ->label('Jenis Kelamin')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email')
                            ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule) {
                                $rule->where('company_id', Filament::getTenant()->id);
                                return $rule;
                            })
                            ->validationMessages([
                                'unique' => 'Email ini sudah terdaftar, silakan gunakan yang lain.',
                            ])
                            ->required()
                            ->email(),
                        TextInput::make('telepon')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->required()
                            ->validationMessages([
                                'required' => 'Telepon tidak boleh kosong',
                                'regex' => 'Nomor Telepon tidak valid',
                                'max' => 'Nomor Telepon terlalu panjang',
                            ])
                            ->maxLength(15)

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

                Hidden::make('company_id')
                    ->default($uuid),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('NIK')->label('NIK')->searchable(),
                TextColumn::make('nama')->label('Nama Lengkap')->searchable()->sortable(),
                TextColumn::make('telepon')->label('No. Telepon')->searchable(),
                TextColumn::make('status_pembayaran_bulan_ini')
                    ->label('Status Pembayaran')
                    ->default('Belum Dibayar')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Lunas' => 'success',
                        'Belum Dibayar' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->dateTime()->label('Tanggal Dibuat')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label('Dihapus pada')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                ActivityLogTimelineTableAction::make('Log'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Pemilik/Investor Alat yang Terdaftar')
            ->emptyStateDescription('Silahkan buat data pemilik/investor baru untuk memulai.')
            ->defaultSort('created_at', 'desc');
    }
    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    public static function getRelations(): array
    {
        return [
            DaftarAlatRelationManager::class,
            RiwayatSewaPemilikRelationManager::class,
            TransaksiPembayaransRelationManager::class,
            ActivitylogRelationManager::class,
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withTrashed();
    }
}
