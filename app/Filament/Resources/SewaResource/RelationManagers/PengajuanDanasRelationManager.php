<?php

namespace App\Filament\Resources\SewaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Level;

class PengajuanDanasRelationManager extends RelationManager
{
    protected static string $relationship = 'pengajuanDanas';
    protected static ?string $title = 'Pengajuan Dana';

    protected static bool $isLazy = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul_pengajuan')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('deskripsi_pengajuan')
                    ->label('Deskripsi Umum')
                    ->columnSpanFull(),

                Forms\Components\Hidden::make('tipe_pengajuan')
                    ->default('project'),
                Forms\Components\Hidden::make('nilai')
                    ->default('0'),
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),
                Select::make('bank_id')
                    ->relationship('bank', 'nama_bank')
                    ->placeholder('Pilih Bank')
                    ->searchable()
                    ->preload()
                    ->label('Daftar Bank')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn(callable $set) => $set('bank_account_id', null)),
                Forms\Components\Select::make('bank_account_id')
                    ->label('Nomor Rekening')
                    ->options(function (callable $get) {
                        $bankId = $get('bank_id');
                        if (!$bankId) {
                            return [];
                        }

                        return \App\Models\BankAccount::where('bank_id', $bankId)
                            ->get()
                            ->mapWithKeys(function ($account) {
                                return [$account->id => "{$account->no_rek} ({$account->nama_pemilik})"];
                            });
                    })
                    ->reactive()
                    ->searchable()
                    ->native(false)
                    ->createOptionForm([
                        Forms\Components\TextInput::make('no_rek')
                            ->label('Nomor Rekening')
                            ->required(),
                        Forms\Components\TextInput::make('nama_pemilik')
                            ->label('Nama Pemilik')
                            ->required(),
                        Forms\Components\Hidden::make('bank_id')
                            ->default(fn(callable $get) => $get('bank_id')), // ambil dari select bank
                        Forms\Components\Hidden::make('user_id')
                            ->default(auth()->id()),
                    ])
                    ->createOptionUsing(function (array $data, callable $get): string {
                        $data['bank_id'] = $get('bank_id');

                        $account = \App\Models\BankAccount::create($data);
                        return $account->id; // UUID
                    })
                    ->required(),
                Repeater::make('detailPengajuans') // nama relasi
                    ->relationship()
                    ->columnSpanFull()
                    ->label('Rincian Pengajuan Dana')
                    ->schema([
                        TextInput::make('deskripsi')
                            ->label('Nama Item')
                            ->required(),
                        TextInput::make('qty')
                            ->label('Jumlah')
                            ->required(),

                        TextInput::make('harga_satuan')
                            ->label('Harga Satuan')
                            ->numeric()
                            ->required(),
                    ])
                    ->defaultItems(1)
                    ->createItemButtonLabel('Tambah Rincian')
                    ->columns(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('judul_pengajuan')
            ->columns([
                Tables\Columns\TextColumn::make('judul_pengajuan'),
                Tables\Columns\TextColumn::make('deskripsi_pengajuan'),
                Tables\Columns\TextColumn::make('bank.nama_bank'),
                Tables\Columns\TextColumn::make('bank.accounts.no_rek')->label('Nomor Rekening'),
                Tables\Columns\TextColumn::make('bank.accounts.nama_pemilik')->label('Nama Pemilik'),
                Tables\Columns\TextColumn::make('user.name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function ($livewire, $record) {
                        $record->updateTotalHarga();

                        $nilai = $record->nilai;

                        $level = Level::where('max_nilai', '>=', $nilai)
                            ->orderBy('max_nilai')
                            ->first();

                        if ($level) {
                            $firstStep = $level->levelSteps()->orderBy('step')->first();
                            $roleName = optional($firstStep?->roles)->id;

                            $record->update([
                                'level_id'     => $level->id,
                                'dalam_review' => $roleName,
                            ]);
                        }
                    }),

            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make()
                    ->after(function ($livewire, $record) {
                        $record->updateTotalHarga();

                        $nilai = $record->nilai;

                        $level = Level::where('max_nilai', '>=', $nilai)
                            ->orderBy('max_nilai')
                            ->first();

                        if ($level) {
                            $firstStep = $level->levelSteps()->orderBy('step')->first();
                            $roleName = optional($firstStep?->roles)->id;

                            $record->update([
                                'level_id'     => $level->id,
                                'dalam_review' => $roleName,
                            ]);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function afterCreate(): void
    {
        $pengajuan = $this->record;

        // Hitung ulang total nilai jika kamu punya relasi detail
        $pengajuan->updateTotalHarga();

        $nilai = $pengajuan->nilai;

        $level = Level::where('max_nilai', '>=', $nilai)
            ->orderBy('max_nilai')
            ->first();

        if ($level) {
            $firstStep = $level->levelSteps()->orderBy('step')->first();
            $roleName = optional($firstStep?->roles)->id;

            $pengajuan->update([
                'level_id'     => $level->id,
                'dalam_review' => $roleName,
            ]);
        }
    }
}
