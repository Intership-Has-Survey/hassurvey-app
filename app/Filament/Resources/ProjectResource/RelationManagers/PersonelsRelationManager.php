<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Personel;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Livewire\Component as Livewire;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;

class PersonelsRelationManager extends RelationManager
{
    protected static string $relationship = 'personels';
    protected static ?string $title = 'Tim Personel Proyek';
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('personel_id')
                    ->helperText(
                        'Untuk memilih personel yang sama, isi terlebih dahulu tanggal_berakhir di project sebelumnya.'
                    )
                    ->options(
                        // Memuat hanya personel dari company yang sama dengan project
                        function () {
                            $project = $this->getOwnerRecord();
                            return Personel::where('company_id', $project->company_id)
                                ->get()
                                ->filter(function ($personel) {
                                    // Sekarang projects sudah di-load, tidak perlu query tambahan
                                    return $personel->status === 'Tersedia';
                                })
                                ->mapWithKeys(function ($personel) {
                                    return [
                                        $personel->id => $personel->nama

                                    ];
                                });
                        }
                    )
                    ->reactive()
                    ->getOptionLabelUsing(function ($value) {
                        // Ini akan handle display saat edit form
                        $personel = Personel::find($value);
                        return $personel ? $personel->nama : $value;
                    })
                    ->label('Pilih Personel')
                    ->searchable()
                    ->disabledOn('edit')
                    ->required()
                    ->afterStateUpdated(function ($state, Set $set) {
                        $set('tanggal_mulai', null);
                    })
                    ->validationMessages([
                        'required' => 'Personel tidak boleh kosong',
                    ]),

                Forms\Components\Select::make('peran')
                    ->options([
                        'surveyor' => 'Surveyor',
                        'asisten surveyor' => 'Asisten Surveyor',
                        'driver' => 'Driver',
                        'drafter' => 'Drafter',
                    ])
                    ->required()
                    ->validationMessages([
                        'required' => 'Peran tidak boleh kosong',
                    ])
                    ->native(false),
                Forms\Components\DatePicker::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->validationMessages([
                        'required' => 'Tanggal mulai tidak boleh kosong',
                        'after_or_equal' => 'Tanggal mulai harus setelah tanggal berakhir project sebelumnya',
                    ])
                    ->reactive()
                    ->default(now())
                    ->minDate(function (Get $get) {
                        $query = $this->getOwnerRecord();
                        // dd([$query->personels(), $this, $livewire->getOwnerRecord(), $livewire]);
                        $q = $query->personels()
                            ->where('personel_id', $get('personel_id'))
                            ->orderBy('tanggal_berakhir', 'desc')
                            ->first();

                        // dd($q);

                        return $q?->tanggal_berakhir ? \Carbon\Carbon::parse($q->tanggal_berakhir)->addDay() : null;
                    })
                    ->disabledOn('edit')
                    ->native(false),
                Forms\Components\DatePicker::make('tanggal_berakhir')
                    ->label('Tanggal Berakhir')
                    ->validationMessages([
                        'required' => 'Tanggal berakhir tidak boleh kosong',
                    ])
                    ->visibleOn('edit')
                    // ->minDate(fn(Get $get) => $get('tgl_mulai'))
                    ->native(false),
                Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }


    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                Tables\Columns\TextColumn::make('personel.nama')
                    ->label('Nama Personel')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('peran')
                    ->label('Peran di Proyek')
                    ->badge(),
                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->date(),
                Tables\Columns\TextColumn::make('tanggal_berakhir')
                    ->label('Tanggal Berakhir')
                    ->date()
                    ->placeholder('Belum Berakhir'),
                // Tables\Columns\TextColumn::make('tenggat_waktu')
                //     ->label('Rentang Waktu (Hari)')
                //     ->state(function ($record) {
                //         $tanggalMulai = $record->pivot->tanggal_mulai;
                //         $tanggalBerakhir = $record->pivot->tanggal_berakhir;

                //         if (!$tanggalMulai || !$tanggalBerakhir) {
                //             return null;
                //         }

                //         $start = new \DateTime($tanggalMulai);
                //         $end = new \DateTime($tanggalBerakhir);
                //         $interval = $start->diff($end);
                //         return $interval->days + 1;
                //     })

                //     ->placeholder('Belum Ditentukan')
                //     ->suffix(' hari'),
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
                Tables\Actions\CreateAction::make(),
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\AttachAction::make()
                //     ->preloadRecordSelect()
                //     ->recordSelectOptionsQuery(function ($query) {
                //         $project = $this->getOwnerRecord();
                //         return $query->where('company_id', $project->company_id);
                //     })
                //     ->form(fn(Tables\Actions\AttachAction $action): array => [
                //         Forms\Components\Placeholder::make('label_personel')
                //             ->label('Pilih Personel'),
                //         // Forms\Components\Placeholder::make('label_personel')
                //         //     ->label('Pilih Personel'),
                //         Forms\Components\Select::make('recordId') // Gunakan recordId langsung
                //             ->label('Personel')
                //             ->options(function () {
                //                 $project = $this->getOwnerRecord();

                //                 return \App\Models\Personel::where('company_id', $project->company_id)
                //                     ->get()
                //                     ->mapWithKeys(function ($personel) {
                //                         return [
                //                             $personel->id => "{$personel->nama} (" . ($personel->status === 'Tersedia' ? 'Tersedia' : 'Dalam Proyek') . ")"

                //                         ];
                //                     });
                //             })
                //             ->searchable()
                //             ->required()
                //             ->validationMessages([
                //                 'required' => 'Personel tidak boleh kosong',
                //             ])
                //             ->native(false),
                //     ])
                //     ->successNotificationTitle('Personel berhasil ditambahkan.')
                //     ->label('Tambah Personel')
                //     ->modalHeading('Tambah Personel ke Proyek')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

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
                        Forms\Components\FileUpload::make('bukti_pembayaran_path')
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
                        TextInput::make('keterangan')
                            ->label('Keterangan')
                            ->maxlength(500)
                            ->disabled()
                            ->nullable(),
                    ])
                    ->visible(function ($record) {
                        $project = $this->getOwnerRecord();
                        return $record->pembayaranPersonel()->exists();
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
                                'bukti_pembayaran_path' => $pembayaran->bukti_pembayaran_path,
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
                        // dd([$record, $project]);
                        //ini agak bener, jika 1 personel maka benar
                        // return $record->personel->pembayaranPersonel()->doesntExist();

                        return $record->pembayaranPersonel()->doesntExist();
                    })
                    // ->visible(function ($record) {
                    //     // dd($record->pembayaranPersonel());
                    //     // return !$record->tanggal_berakhir;
                    // })
                    // ->visible(fn($record) => !$record->pembayaranPersonel())
                    ->label('Bayar')
                    ->icon('heroicon-m-banknotes')
                    ->form([
                        Forms\Components\TextInput::make('nilai')
                            ->numeric()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->required()
                            ->prefix('Rp')
                            ->validationMessages([
                                'required' => 'Nilai harus diisi',
                            ]),
                        Forms\Components\FileUpload::make('bukti_pembayaran_path')
                            ->label('Bukti Pembayaran')
                            ->image()
                            ->maxSize(1024)
                            ->disk('public')
                            ->directory('bukti-pembayaran')
                            ->columnSpanFull()
                            ->validationMessages([
                                'max' => 'Ukuran file maksimal 1 MB',
                            ]),
                        Forms\Components\DatePicker::make('tanggal_transaksi')
                            ->label('Tanggal Transaksi')
                            ->required()
                            ->validationMessages([
                                'required' => 'Tanggal transaksi harus diisi',
                            ])
                            ->default(now()),
                        Forms\Components\Hidden::make('user_id')
                            ->default(auth()->id()),
                        Forms\Components\Select::make('metode_pembayaran')
                            ->label('Metode Pembayaran')
                            ->required()
                            ->options([
                                'transfer' => 'Transfer',
                                'tunai' => 'Tunai',
                            ])
                            ->validationMessages([
                                'required' => 'Metode pembayaran harus dipilih',
                            ]),
                        TextInput::make('keterangan')
                            ->label('Keterangan')
                            ->maxlength(500)
                            ->nullable(),
                        Hidden::make('company_id')->default(fn() => $this->getOwnerRecord()->company_id),
                    ])
                    ->action(function (array $data, $record) {
                        // Simpan ke tabel pembayaran_personel
                        $project = $this->getOwnerRecord();
                        $pembayaran = \App\Models\PembayaranPersonel::create([
                            'personel_project_id' => $record->id,
                            'project_id' => $project->id,
                            'personel_id' => $record->id,
                            'nilai' => $data['nilai'],
                            'bukti_pembayaran_path' => $data['bukti_pembayaran_path'] ?? null,
                            'tanggal_transaksi' => $data['tanggal_transaksi'],
                            'metode_pembayaran' => $data['metode_pembayaran'],
                            'user_id' => $data['user_id'],
                        ]);
                        $pembayaran->statusPengeluarans()->create([
                            'user_id' => $data['user_id'],
                            'nilai' => $data['nilai'],
                            'tanggal_transaksi' => $data['tanggal_transaksi'],
                            'metode_pembayaran' => $data['metode_pembayaran'],
                            'bukti_pembayaran_path' => $data['bukti_pembayaran_path'] ?? null,
                            'company_id' => $data['company_id'] ?? $project->company_id,
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
