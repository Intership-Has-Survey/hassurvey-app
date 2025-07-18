<?php

namespace App\Filament\Resources\CorporateResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use App\Models\TrefRegion;
use App\Models\Perorangan;

// Import Action yang dibutuhkan
use Filament\Tables\Actions\AttachAction;

class PeroranganRelationManager extends RelationManager
{
    protected static string $relationship = 'perorangan';
    protected static ?string $title = 'PIC';
    protected static bool $isLazy = false;
    public function form(Form $form): Form
    {
        // Form ini akan digunakan untuk membuat PIC baru dan mengedit PIC yang sudah ada.
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nik')
                    ->nullable()
                    ->maxLength(16)
                    ->unique(ignoreRecord: true) // ignoreRecord penting untuk edit
                    ->label('NIK'),
                Forms\Components\Select::make('gender')
                    ->options([
                        'Pria' => 'Pria',
                        'Wanita' => 'Wanita',
                    ])
                    ->required()
                    ->label('Jenis Kelamin'),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true), // ignoreRecord penting untuk edit
                Forms\Components\TextInput::make('telepon')
                    ->tel()
                    ->required()
                    ->label('Nomor Telepon'),
                Section::make('Alamat PIC')
                    ->schema([
                        Select::make('provinsi')
                            ->label('Provinsi')
                            ->required()
                            ->placeholder('Pilih Provinsi')
                            ->options(TrefRegion::query()->where(DB::raw('LENGTH(code)'), 2)->pluck('name', 'code'))
                            ->live()
                            ->searchable()
                            ->afterStateUpdated(function (Set $set) {
                                $set('kota', null);
                                $set('kecamatan', null);
                                $set('desa', null);
                            }),
                        Select::make('kota')
                            ->label('Kota/Kabupaten')
                            ->required()
                            ->placeholder('Pilih Kota/Kabupaten')
                            ->options(function (Get $get) {
                                $provinceCode = $get('provinsi');
                                if (!$provinceCode)
                                    return [];
                                return TrefRegion::query()
                                    ->where('code', 'like', $provinceCode . '.%')
                                    ->where(DB::raw('LENGTH(code)'), 5)
                                    ->pluck('name', 'code');
                            })
                            ->live()
                            ->searchable()
                            ->afterStateUpdated(function (Set $set) {
                                $set('kecamatan', null);
                                $set('desa', null);
                            }),
                        Select::make('kecamatan')
                            ->label('Kecamatan')
                            ->required()
                            ->placeholder('Pilih Kecamatan')
                            ->options(function (Get $get) {
                                $regencyCode = $get('kota');
                                if (!$regencyCode)
                                    return [];
                                return TrefRegion::query()
                                    ->where('code', 'like', $regencyCode . '.%')
                                    ->where(DB::raw('LENGTH(code)'), 8)
                                    ->pluck('name', 'code');
                            })
                            ->live()
                            ->searchable()
                            ->afterStateUpdated(function (Set $set) {
                                $set('desa', null);
                            }),
                        Select::make('desa')
                            ->label('Desa/Kelurahan')
                            ->required()
                            ->placeholder('Pilih Desa/Kelurahan')
                            ->options(function (Get $get) {
                                $districtCode = $get('kecamatan');
                                if (!$districtCode)
                                    return [];
                                return TrefRegion::query()
                                    ->where('code', 'like', $districtCode . '.%')
                                    ->where(DB::raw('LENGTH(code)'), 13)
                                    ->pluck('name', 'code');
                            })
                            ->live()
                            ->searchable(),
                        Textarea::make('detail_alamat')
                            ->label('Detail Alamat')
                            ->required()
                            ->placeholder('Masukkan detail alamat')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('PIC (Person in Charge)')
            ->recordTitleAttribute('nama')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telepon'),
            ])
            ->filters([
                //
            ])
            ->headerActions([

                // Mengganti CreateAction menjadi AttachAction
                AttachAction::make()
                    ->label('Tambahkan/Pilih PIC')
                    ->form(fn(AttachAction $action): array => [
                        // Form untuk memilih PIC yang sudah ada
                        $action->getRecordSelect()
                            ->label('Pilih PIC')
                            ->createOptionForm(fn(Form $form) => $this->form($form))
                            ->createOptionAction(fn(Forms\Components\Actions\Action $action) => $action->label('Buat PIC Baru'))
                            // FIX: Tambahkan closure ini untuk menangani pembuatan record baru
                            ->createOptionUsing(function (array $data): string {
                                // Tambahkan user_id secara otomatis
                                $data['user_id'] = auth()->id();
                                return Perorangan::create($data)->getKey();
                            }),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Mengganti DeleteAction menjadi DetachAction
                Tables\Actions\DetachAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Mengganti DeleteBulkAction menjadi DetachBulkAction
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
