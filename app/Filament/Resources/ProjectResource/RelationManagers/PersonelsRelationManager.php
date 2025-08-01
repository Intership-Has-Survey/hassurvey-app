<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;

class PersonelsRelationManager extends RelationManager
{
    protected static string $relationship = 'personels';

    protected static ?string $title = 'Tim Personel Proyek';

    protected static bool $isLazy = false;

    // Form ini hanya digunakan untuk MENGEDIT data pivot (peran)
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form ini tidak lagi digunakan secara langsung.
                // Logika form dipindahkan ke AttachAction dan EditAction.
            ]);
    }


    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                Tables\Columns\TextColumn::make('nama'),
                // Tables\Columns\TextColumn::make('status')->badge()
                //     ->color(
                //         fn(string $state): string => $state === 'Tersedia' ? 'success' : 'warning',
                //     ),
                // Menampilkan data 'peran' dari tabel pivot
                Tables\Columns\TextColumn::make('pivot.peran')
                    ->label('Peran di Proyek')
                    ->badge(),
                Tables\Columns\TextColumn::make('pivot.tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->date(),
                Tables\Columns\TextColumn::make('pivot.tanggal_berakhir')
                    ->label('Tanggal Berakhir')
                    ->date()
                    ->placeholder('Belum Berakhir'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn(Tables\Actions\AttachAction $action): array => [
                        Forms\Components\Placeholder::make('label_personel')
                            ->label('Pilih Personel'),
                        $action
                            ->getRecordSelect(),
                        Forms\Components\Select::make('peran')
                            ->options([
                                'surveyor' => 'Surveyor',
                                'asisten surveyor' => 'Asisten Surveyor',
                                'driver' => 'Driver',
                                'drafter' => 'Drafter',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->default(now())
                            ->native(false),
                        Hidden::make('user_id')
                            ->default(auth()->id()),
                    ])
                    ->successNotificationTitle('Personel berhasil ditambahkan.')
                    ->label('Tambah Personel')
                    ->modalHeading('Tambah Personel ke Proyek')


            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form(fn(Tables\Actions\EditAction $action): array => [
                        Forms\Components\Select::make('peran')
                            ->options([
                                'surveyor' => 'Surveyor',
                                'asisten surveyor' => 'Asisten Surveyor',
                                'driver' => 'Driver',
                                'drafter' => 'Drafter',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->disabled()
                            ->native(false),
                        Forms\Components\DatePicker::make('tanggal_berakhir')
                            ->label('Tanggal Berakhir')
                            ->native(false),
                    ]),
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('sudah_dibayar')
                    ->label('Terbayar')
                    ->icon('heroicon-m-check-badge')
                    ->color('gray')
                    ->form([
                        TextInput::make('nilai')
                            ->numeric()
                            ->disabled()
                            ->required(),
                        Forms\Components\FileUpload::make('bukti_bayar')
                            ->disk('public') // sesuaikan dengan disk kamu
                            ->disabled()
                            ->directory('bukti-bayar'),
                        Forms\Components\DatePicker::make('tanggal_bayar')
                            ->disabled()
                            ->required(),
                        Forms\Components\Hidden::make('user_id')
                            ->disabled()
                            ->default(auth()->id()),
                        TextInput::make('nama_bank')
                            ->disabled(),
                        TextInput::make('no_rek')
                            ->disabled(),
                    ])
                    ->visible(function ($record) {
                        $project = $this->getOwnerRecord();
                        return $record->pembayaranPersonel()
                            ->where('personel_id', $record->id)
                            ->where('project_id', $project->id)
                            ->exists();
                    })
                    ->mountUsing(function ($form, $record) {
                        // $pembayaran = $record->pembayaranPersonel;
                        // $pembayaran = $record->pembayaranPersonel()
                        //     ->where('personel_project_id', $record->id)->latest()->first();
                        $project = $this->getOwnerRecord();
                        $pembayaran = $record->pembayaranPersonel()
                            ->where('personel_id', $record->id)
                            ->where('project_id', $project->id)
                            ->latest()
                            ->first();
                        // dd($pembayaran);
                        if ($pembayaran) {
                            $form->fill([
                                'nilai' => $pembayaran->nilai,
                                'bukti_bayar' => $pembayaran->bukti_pembayaran,
                                'tanggal_bayar' => $pembayaran->tanggal_bayar,
                                'user_id' => $pembayaran->user_id,
                                'nama_bank' => $pembayaran->bank_id, // atau ambil relasi bank->nama_bank
                                'no_rek' => $pembayaran->bank_account_id, // atau bank_account->no_rek
                            ]);
                        }
                    })
                    ->action(function ($record) {
                        $pembayaran = $record->pembayaranPersonel;
                        // dd($pembayaran); // akan dieksekusi saat tombol/icon diklik
                    }),

                Tables\Actions\Action::make('bayar')
                    ->visible(function ($record) {
                        $project = $this->getOwnerRecord();
                        return $record->pembayaranPersonel()
                            ->where('personel_id', $record->id)
                            ->where('project_id', $project->id)
                            ->doesntExist();
                    })
                    ->label('Bayar')
                    ->icon('heroicon-m-banknotes')
                    ->form([
                        TextInput::make('nilai')
                            ->numeric()
                            ->required(),

                        Forms\Components\FileUpload::make('bukti_bayar')
                            ->disk('public') // sesuaikan dengan disk kamu
                            ->directory('bukti-bayar'),
                        // ->required(),

                        Forms\Components\DatePicker::make('tanggal_bayar')
                            ->label('Tanggal transaksi')
                            ->required(),
                        Forms\Components\Hidden::make('user_id')
                            ->default(auth()->id()),
                        // ->required(),

                        Forms\Components\Select::make('nama_bank')
                            ->label('Metode Pembayaran')
                            ->options([
                                'transfer' => 'Transfer',
                                'tunai' => 'Tunai',
                            ]),

                        Forms\Components\Select::make('no_rek')
                            ->label('Metode Pembayaran')
                            ->options([
                                'transfer' => 'Transfer',
                                'tunai' => 'Tunai',
                            ]),
                        // ->required(),
                    ])
                    ->action(function (array $data, $record) {
                        // Simpan ke tabel pembayaran_personel
                        $project = $this->getOwnerRecord();
                        // dd($project);
                        $pembayaran = \App\Models\PembayaranPersonel::create([
                            'personel_project_id' => $record->id,
                            'project_id' => $project->id,
                            'personel_id' => $record->id,
                            'nilai' => $data['nilai'],
                            'bukti_pembayaran' => $data['bukti_bayar'],
                            'tanggal_bayar' => $data['tanggal_bayar'],
                            'bank_id' => $data['nama_bank'],
                            'bank_account_id' => $data['no_rek'],
                            'user_id' => $data['user_id'],
                        ]);

                        $pembayaran->statusPengeluarans()->create([
                            'user_id' => $data['user_id'], // atau auth()->id()
                            'nilai' => $data['nilai'],
                            'tanggal_transaksi' => $data['tanggal_bayar'],
                            'metode_pembayaran' => 'Transfer Bank', // atau bisa juga pakai enum atau TextInput
                            'bukti_pembayaran_path' => $data['bukti_bayar'],
                        ]);
                    })
                    ->color('success')
                    ->modalHeading('Bayar Personel'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
