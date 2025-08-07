<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;

class PersonelsRelationManager extends RelationManager
{
    protected static string $relationship = 'personels';
    protected static ?string $title = 'Tim Personel Proyek';
    protected static bool $isLazy = false;
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
                Tables\Columns\TextColumn::make('tenggat_waktu')
                    ->label('Tenggat Waktu (Hari)')
                    ->state(function ($record) {
                        $tanggalMulai = $record->pivot->tanggal_mulai;
                        $tanggalBerakhir = $record->pivot->tanggal_berakhir;

                        if (!$tanggalMulai || !$tanggalBerakhir) {
                            return null;
                        }

                        $start = new \DateTime($tanggalMulai);
                        $end = new \DateTime($tanggalBerakhir);
                        $interval = $start->diff($end);
                        return $interval->days + 1;
                    })

                    ->placeholder('Belum Ditentukan')
                    ->suffix(' hari'),
            ])
            ->filters([
                Tables\Filters\Filter::make('sudah_dibayar')
                    ->label('Hanya yang Sudah Dibayar')
                    ->query(function ($query) {
                        $project = request()->route('record'); // atau $this->getOwnerRecord() jika dalam Resource
                        return $query->whereHas('pembayaranPersonel', function ($q) use ($project) {
                            $q->where('project_id', $project->id);
                        });
                    }),
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
                        Forms\Components\TextInput::make('nilai')
                            ->numeric()
                            ->mask(RawJs::make('$money($input)'))
                            ->disabled()
                            ->stripCharacters(','),
                        Forms\Components\FileUpload::make('bukti_bayar')
                            ->disk('public')
                            ->disabled()
                            ->directory('bukti-pembayaran'),
                        Forms\Components\DatePicker::make('tanggal_transaksi')
                            ->disabled()
                            ->required(),
                        Forms\Components\Hidden::make('user_id')
                            ->disabled()
                            ->default(auth()->id()),
                        Forms\Components\Select::make('metode_pembayaran')
                            ->disabled()
                            ->options([
                                'transfer' => 'Transfer',
                                'tunai' => 'Tunai',
                            ]),
                        TextColumn::make('keterangan')
                            ->label('Keterangan')
                            ->maxlength(500)
                            ->disabled()
                            ->nullable(),
                    ])
                    ->visible(function ($record) {
                        $project = $this->getOwnerRecord();
                        return $record->pembayaranPersonel()
                            ->where('personel_id', $record->id)
                            ->where('project_id', $project->id)
                            ->exists();
                    })
                    ->mountUsing(function ($form, $record) {
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
                                'bukti_pembayaran' => $pembayaran->bukti_pembayaran,
                                'tanggal_transaksi' => $pembayaran->tanggal_transaksi,
                                'user_id' => $pembayaran->user_id,
                                'metode_pembayaran' => $pembayaran->metode_pembayaran, // atau bank_account->no_rek
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
                        Forms\Components\TextInput::make('nilai')
                            ->numeric()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(','),
                        Forms\Components\FileUpload::make('bukti_bayar')
                            ->disk('public') // sesuaikan dengan disk kamu
                            ->directory('bukti-bayar'),
                        Forms\Components\DatePicker::make('tanggal_transaksi')
                            ->label('Tanggal transaksi')
                            ->required(),
                        Forms\Components\Hidden::make('user_id')
                            ->default(auth()->id()),
                        Forms\Components\Select::make('metode_pembayaran')
                            ->label('Metode Pembayaran')
                            ->options([
                                'transfer' => 'Transfer',
                                'tunai' => 'Tunai',
                            ]),
                        TextColumn::make('keterangan')
                            ->label('Keterangan')
                            ->maxlength(500)
                            ->nullable(),
                    ])
                    ->action(function (array $data, $record) {
                        // Simpan ke tabel pembayaran_personel
                        $project = $this->getOwnerRecord();
                        $pembayaran = \App\Models\PembayaranPersonel::create([
                            'personel_project_id' => $record->id,
                            'project_id' => $project->id,
                            'personel_id' => $record->id,
                            'nilai' => $data['nilai'],
                            'bukti_pembayaran_path' => $data['bukti_bayar'],
                            'tanggal_transaksi' => $data['tanggal_transaksi'],
                            'metode_pembayaran' => $data['metode_pembayaran'],
                            'user_id' => $data['user_id'],
                        ]);
                        $pembayaran->statusPengeluarans()->create([
                            'user_id' => $data['user_id'], // atau auth()->id()
                            'nilai' => $data['nilai'],
                            'tanggal_transaksi' => $data['tanggal_transaksi'],
                            'metode_pembayaran' => $data['metode_pembayaran'], // atau bisa juga pakai enum atau TextInput
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
