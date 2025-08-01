<?php

namespace App\Filament\Resources\PersonelResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\RawJs;

class ProjectPersonelRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';
    protected static ?string $title = 'Riwayat Projek';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                Tables\Columns\TextColumn::make('nama_project'),
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
                Tables\Filters\Filter::make('sudah_dibayar')
                    ->label('Sudah terbayar')
                    ->query(function ($query) {
                        $personel = $this->getOwnerRecord(); // Model Personel

                        return $query->whereExists(function ($subQuery) use ($personel) {
                            $subQuery->selectRaw(1)
                                ->from('pembayaran_personels as p')
                                ->whereColumn('p.project_id', 'projects.id')
                                ->where('p.personel_id', $personel->id);
                        });
                    }),
                Tables\Filters\Filter::make('belum_dibayar')
                    ->label('Belum dibayar')
                    ->query(function ($query) {
                        $personel = $this->getOwnerRecord(); // Model Personel

                        return $query->whereNotExists(function ($subQuery) use ($personel) {
                            $subQuery->selectRaw(1)
                                ->from('pembayaran_personels as p')
                                ->whereColumn('p.project_id', 'projects.id')
                                ->where('p.personel_id', $personel->id);
                        });
                    })
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),

                Tables\Actions\Action::make('bayar')
                    ->visible(function ($record) {
                        $personel = $this->getOwnerRecord();
                        // dd($personel);
                        return $personel->pembayaranPersonel()
                            ->where('project_id', $record->id)
                            ->where('personel_id', $personel->id)
                            ->doesntExist();
                    })
                    ->label('Bayar')
                    ->icon('heroicon-m-banknotes')
                    ->form([
                        Forms\Components\DatePicker::make('tanggal_transaksi')
                            ->label('Tanggal transaksi')
                            ->required(),
                        Forms\Components\Select::make('metode_pembayaran')
                            ->label('Metode Pembayaran')
                            ->options([
                                'transfer' => 'Transfer',
                                'tunai' => 'Tunai',
                            ]),
                        Forms\Components\TextInput::make('nilai')
                            ->numeric()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->required(),
                        Forms\Components\FileUpload::make('bukti_bayar')
                            ->disk('public') // sesuaikan dengan disk kamu
                            ->directory('bukti-bayar'),
                        Forms\Components\Hidden::make('user_id')
                            ->default(auth()->id()),
                    ])
                    ->action(function (array $data, $record) {
                        // Simpan ke tabel pembayaran_personel
                        $personel = $this->getOwnerRecord();
                        $pembayaran = \App\Models\PembayaranPersonel::create([
                            'personel_project_id' => $record->id,
                            'project_id' => $record->id,
                            'personel_id' => $personel->id,
                            'nilai' => $data['nilai'],
                            'bukti_pembayaran_path' => $data['bukti_bayar'],
                            'tanggal_transaksi' => $data['tanggal_transaksi'],
                            'metode_pembayaran' => $data['metode_pembayaran'],
                            // 'bank_account_id' => $data['no_rek'],
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
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('sudah_dibayar')
                    ->label('Terbayar')
                    ->icon('heroicon-m-check-badge')
                    ->color('gray')
                    ->form([
                        Forms\Components\DatePicker::make('tanggal_transaksi')
                            ->disabled()
                            ->required(),
                        Forms\Components\Select::make('metode_pembayaran')
                            ->label('Metode Pembayaran')
                            ->options([
                                'transfer' => 'Transfer',
                                'tunai' => 'Tunai',
                            ])
                            ->disabled(),
                        Forms\Components\TextInput::make('nilai')
                            ->numeric()
                            ->disabled()
                            ->required(),
                        Forms\Components\FileUpload::make('bukti_bayar')
                            ->disk('public') // sesuaikan dengan disk kamu
                            ->disabled()
                            ->directory('bukti-pembayaran'),
                    ])
                    ->visible(function ($record) {
                        $personel = $this->getOwnerRecord();
                        // dd($personel);
                        return $personel->pembayaranPersonel()
                            ->where('project_id', $record->id)
                            ->where('personel_id', $personel->id)
                            ->exists();
                    })
                    ->mountUsing(function ($form, $record) {
                        $personel = $this->getOwnerRecord();
                        $pembayaran = $personel->pembayaranPersonel()
                            ->where('personel_id', $personel->id)
                            ->where('project_id', $record->id)
                            ->latest()
                            ->first();
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



            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
