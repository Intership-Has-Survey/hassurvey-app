<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Level;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\BankAccount;
use Filament\Support\RawJs;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use App\Filament\Resources\PengajuanDanaResource\RelationManagers\DetailPengajuansRelationManager;


class PengajuanDanasRelationManager extends RelationManager
{
    protected static string $relationship = 'pengajuanDanas';
    protected static ?string $title = 'Pengajuan Dana';

    protected static bool $isLazy = false;
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('judul_pengajuan')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('deskripsi_pengajuan')
                    ->label('Deskripsi Umum')
                    ->columnSpanFull(),

                Hidden::make('tipe_pengajuan')
                    ->default('project'),
                Hidden::make('nilai')
                    ->default('0'),
                Hidden::make('user_id')
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
                Select::make('bank_account_id')
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
                    ->createOptionForm([
                        TextInput::make('no_rek')
                            ->label('Nomor Rekening')
                            ->required(),
                        TextInput::make('nama_pemilik')
                            ->label('Nama Pemilik')
                            ->required(),
                        Hidden::make('bank_id')
                            ->default(fn(callable $get) => $get('bank_id')),
                        Hidden::make('user_id')
                            ->default(auth()->id()),
                    ])
                    ->createOptionUsing(function (array $data, callable $get): string {
                        // Ambil bank_id dari form utama
                        $data['bank_id'] = $get('bank_id');

                        $account = \App\Models\BankAccount::create($data);
                        return $account->id; // UUID
                    })
                    ->searchable()
                    ->native(false)
                    ->required(),


                Repeater::make('detailPengajuans')
                    ->relationship()
                    ->columnSpanFull()
                    ->label('Rincian Pengajuan Dana')
                    ->schema([
                        TextInput::make('deskripsi')
                            ->label('Nama Item')
                            ->required(),
                        TextInput::make('qty')
                            ->label('Jumlah')
                            ->numeric()
                            ->required(),

                        TextInput::make('harga_satuan')
                            ->label('Harga Satuan')
                            ->numeric()
                            ->prefix('Rp ')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
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
                TextColumn::make('judul_pengajuan'),
                TextColumn::make('deskripsi_pengajuan'),
                TextColumn::make('bank.nama_bank'),
                TextColumn::make('bank.accounts.no_rek')->label('Nomor Rekening'),
                TextColumn::make('bank.accounts.nama_pemilik')->label('Nama Pemilik'),
                TextColumn::make('user.name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->after(function ($livewire, $record) {
                        $record->updateTotalHarga();

                        $nilai = $record->nilai;

                        $level = Level::where('max_nilai', '>=', $nilai)
                            ->orderBy('max_nilai')
                            ->first();

                        if ($level) {
                            $firstStep = $level->levelSteps()->orderBy('step')->first();
                            $roleName = $firstStep->role_id;

                            $record->update([
                                'level_id'     => $level->id,
                                'dalam_review' => $roleName,
                            ]);
                        }
                    }),

            ])
            ->actions([
                DeleteAction::make(),
                EditAction::make()
                    ->after(function ($livewire, $record) {
                        $record->updateTotalHarga();

                        $nilai = $record->nilai;

                        $level = Level::where('max_nilai', '>=', $nilai)
                            ->orderBy('max_nilai')
                            ->first();

                        if ($level) {
                            $firstStep = $level->levelSteps()->orderBy('step')->first();
                            $roleName = $firstStep->role_id;

                            $record->update([
                                'level_id'     => $level->id,
                                'dalam_review' => $roleName,
                            ]);
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getRelations(): array
    {
        return [
            DetailPengajuansRelationManager::class,
        ];
    }

    protected function afterCreate(): void
    {
        $pengajuan = $this->record;
        $pengajuan->updateTotalHarga();
        $nilai = $pengajuan->nilai;
        $level = Level::where('max_nilai', '>=', $nilai)
            ->orderBy('max_nilai')
            ->first();
        if ($level) {
            $firstStep = $level->levelSteps()->orderBy('step')->first();
            $roleName = $firstStep->role_id;
            $pengajuan->update([
                'level_id'     => $level->id,
                'dalam_review' => $roleName,
            ]);
        }
    }
}
