<?php

namespace App\Traits;

use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\TrefRegion;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Facades\Filament;
use Illuminate\Validation\Rules\Unique;

trait GlobalForms
{
    private static function getCorporateForm(): array
    {
        return [
            Section::make('Informasi Perusahaan')
                ->schema([
                    TextInput::make('nama')
                        ->label('Nama Perusahaan')
                        ->maxLength(200)
                        ->required(),
                    TextInput::make('nib')
                        ->label('NIB')
                        ->maxLength(20)
                        ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule) {
                            $rule->where('company_id', Filament::getTenant()->id);
                            return $rule;
                        })
                        ->validationMessages([
                            'unique' => 'NIB ini sudah terdaftar, silakan gunakan yang lain.',
                        ]),
                    Select::make('level')
                        ->label('Level Perusahaan')
                        ->options([
                            'kecil' => 'Kecil',
                            'menengah' => 'Menengah',
                            'besar' => 'Besar',
                        ]),
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
                        ->label('Telepon')
                        ->tel()
                        ->maxLength(15),
                ])->columns(2),

            Section::make('Alamat Perusahaan')
                ->schema(self::getAddressFields())
                ->columns(2),

            Hidden::make('user_id')
                ->default(auth()->id()),

            Hidden::make('company_id')
                ->default(fn() => \Filament\Facades\Filament::getTenant()?->getKey()),

        ];
    }

    private static function getPeroranganForm(): array
    {
        return [
            Section::make('Informasi Perorangan')
                ->schema([
                    TextInput::make('nama')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(100),
                    TextInput::make('nik')
                        ->label('Nomor Induk Kependudukan (NIK)')
                        ->length(16)
                        ->rule('regex:/^\d+$/')
                        ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule) {
                            $rule->where('company_id', Filament::getTenant()->id);
                            return $rule;
                        })
                        ->validationMessages([
                            'unique' => 'NIK sudah pernah terdaftar',
                            'regex' => 'NIK hanya boleh berisi angka',
                        ]),
                    TextInput::make('email')
                        ->label('Email')
                        ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule) {
                            $rule->where('company_id', Filament::getTenant()->id);
                            return $rule;
                        })
                        ->validationMessages([
                            'unique' => 'Email ini sudah terdaftar, silakan gunakan yang lain.',
                        ])
                        ->email(),
                    TextInput::make('telepon')
                        ->label('Telepon')
                        ->tel()
                        ->maxLength(15),
                    Select::make('gender')
                        ->label('Jenis Kelamin')
                        ->options([
                            'Pria' => 'Pria',
                            'Wanita' => 'Wanita',
                        ]),
                ])->columns(2),

            Section::make('Alamat')
                ->schema(self::getAddressFields())
                ->columns(2),

            Hidden::make('user_id')
                ->default(auth()->id()),

            Hidden::make('company_id')
                ->default(fn() => \Filament\Facades\Filament::getTenant()?->getKey()),

        ];
    }

    private static function getAddressFields(): array
    {
        return [
            Select::make('provinsi')
                ->label('Provinsi')

                ->placeholder('Pilih provinsi')
                ->options(TrefRegion::query()
                    ->where(DB::raw('LENGTH(code)'), 2)
                    ->pluck('name', 'code'))
                ->live()
                ->searchable()
                ->afterStateUpdated(function (Set $set) {
                    $set('kota', null);
                    $set('kecamatan', null);
                    $set('desa', null);
                }),

            Select::make('kota')
                ->label('Kota/Kabupaten')

                ->placeholder('Pilih kota/kabupaten')
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

                ->placeholder('Pilih kecamatan')
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

                ->placeholder('Pilih desa/kelurahan')
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

                ->placeholder('Masukkan detail alamat lengkap')
                ->label('Detail Alamat')
                ->rows(3)
                ->columnSpanFull(),
            Hidden::make('company_id')
                ->default(fn() => \Filament\Facades\Filament::getTenant()?->getKey()),

        ];
    }

    private static function getSalesForm(): array
    {
        return [
            Section::make('Informasi Sales')
                ->schema([
                    TextInput::make('nama')
                        ->label('Nama Sales')
                        ->required()
                        ->maxLength(100),
                    TextInput::make('nik')
                        ->label('Nomor Induk Kependudukan (NIK)')
                        ->length(16)
                        ->required()
                        ->rule('regex:/^\d+$/')
                        ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule) {
                            $rule->where('company_id', Filament::getTenant()->id);
                            return $rule;
                        })
                        ->validationMessages([
                            'unique' => 'NIK sudah pernah terdaftar',
                            'regex' => 'NIK hanya boleh berisi angka',
                            'required' => 'NIK wajib diisi',
                        ]),
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule) {
                            $rule->where('company_id', Filament::getTenant()->id);
                            return $rule;
                        })
                        ->required()
                        ->maxLength(100)
                        ->validationMessages([
                            'unique' => 'Email sudah digunakan',
                            'required' => 'Email wajib diisi',
                        ]),
                    TextInput::make('telepon')
                        ->required()
                        ->label('Telepon')
                        ->tel()
                        ->maxLength(15),
                ])->columns(2),

            Section::make('Alamat Sales')
                ->schema(self::getAddressFields())
                ->columns(2),

            Hidden::make('user_id')
                ->default(auth()->id()),

            Hidden::make('company_id')
                ->default(fn() => \Filament\Facades\Filament::getTenant()?->getKey()),

        ];
    }

    private static function getKategoriForm(): array
    {
        return [
            Section::make('Informasi Kategori')
                ->schema([
                    TextInput::make('nama')
                        ->label('Nama Kategori')
                        ->nullable()
                        ->required()
                        ->maxLength(100),
                    Textarea::make('keterangan')
                        ->label('Deskripsi')
                        ->maxLength(500)
                        ->nullable(),
                ])->columns(2),

            Hidden::make('user_id')
                ->default(auth()->id()),
        ];
    }

    private static function getPengajuanDanaForm(): array
    {
        return [
            Section::make('Informasi Pengajuan Dana')
                ->schema([
                    TextInput::make('judul_pengajuan')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Textarea::make('deskripsi_pengajuan')
                        ->label('Deskripsi Umum')
                        ->columnSpanFull(),
                    Hidden::make('nilai')
                        ->default('0'),
                    Hidden::make('user_id')
                        ->default(auth()->id()),
                ]),
            Section::make('Informasi Rekening Penerima')
                ->schema([
                    Select::make('bank_id')
                        ->relationship('bank', 'nama_bank')
                        ->placeholder('Pilih Bank')
                        ->searchable()
                        ->preload()
                        ->label('Daftar Bank')
                        ->required()
                        ->reactive()
                        ->validationMessages([
                            'required' => 'Bank wajib diisi',
                        ])
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
                                ->numeric()
                                ->required()
                                ->placeholder('Contoh: 1234567890')
                                ->validationMessages([
                                    'required' => 'Nomor rekening wajib diisi',
                                ]),
                            TextInput::make('nama_pemilik')
                                ->label('Nama Pemilik')
                                ->required(),
                            Hidden::make('bank_id')
                                ->default(fn(callable $get) => $get('bank_id')),
                            Hidden::make('user_id')
                                ->default(auth()->id()),
                        ])
                        ->createOptionUsing(function (array $data, callable $get): string {
                            $data['bank_id'] = $get('bank_id');

                            $account = \App\Models\BankAccount::create($data);
                            return $account->id; // UUID
                        })
                        ->searchable()
                        ->native(false)
                        ->required()
                        ->visible(fn(callable $get) => !empty($get('bank_id')))
                        ->validationMessages([
                            'required' => 'Nomor Rekening wajib diisi',
                        ]),
                ]),

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

                    Textinput::make('satuan')->required()->placeholder('Kg/Liter/Lembar...'),

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
                ->columns(4),
            Hidden::make('company_id')
                ->default(fn() => Filament::getTenant()?->getKey()),

        ];
    }

    private static function getCustomerForm(): array
    {
        return [
            Select::make('customer_flow_type')
                ->label('Tipe Customer')
                ->options(['perorangan' => 'Perorangan', 'corporate' => 'Corporate'])
                ->live()->dehydrated(false)->native(false)->required()
                ->afterStateUpdated(fn(Set $set) => $set('corporate_id', null))
                ->validationMessages([
                    'required' => 'Customer tidak boleh kosong',
                ]),

            Select::make('corporate_id')
                ->relationship('corporate', 'nama', fn($query) => $query->where('company_id', \Filament\Facades\Filament::getTenant()?->getKey()))
                ->label('Pilih Perusahaan')
                ->live()
                ->searchable()
                ->preload()
                ->createOptionForm(self::getCorporateForm())
                ->afterStateUpdated(function ($state, callable $set) {
                    if (!$state) {
                        $set('perorangan', []);
                        return;
                    }

                    $corporate = \App\Models\Corporate::with('perorangan')->find($state);

                    if (!$corporate) {
                        $set('perorangan', []);
                        return;
                    }

                    $perorangan = $corporate->perorangan->map(fn($p) => [
                        'perorangan_id' => $p->id,
                    ])->toArray();

                    $set('perorangan', $perorangan);
                })
                ->required(fn(Get $get) => $get('customer_flow_type') === 'corporate')
                ->validationMessages([
                    'required' => 'Perusahaan wajib diisi',
                ])
                ->visible(fn(Get $get) => $get('customer_flow_type') === 'corporate'),

            Repeater::make('perorangan')
                ->label(fn(Get $get): string => $get('customer_flow_type') === 'corporate' ? 'PIC' : 'Pilih Customer')
                ->relationship()
                ->schema([
                    Select::make('perorangan_id')
                        ->label(false)
                        ->options(function (Get $get, $state): array {
                            $selectedPicIds = collect($get('../../perorangan'))->pluck('perorangan_id')->filter()->all();
                            $selectedPicIds = array_diff($selectedPicIds, [$state]);
                            return \App\Models\Perorangan::where('company_id', \Filament\Facades\Filament::getTenant()?->getKey())
                                ->whereNotIn('id', $selectedPicIds)
                                ->get()
                                ->mapWithKeys(fn($p) => [$p->id => "{$p->nama} - {$p->nik}"])->all();
                        })
                        ->searchable()
                        ->createOptionForm(self::getPeroranganForm())
                        ->createOptionUsing(fn(array $data): string => \App\Models\Perorangan::create($data)->id)
                        ->required()
                        ->validationMessages([
                            'required' => 'Kolom Customer wajib diisi',
                        ])
                        ->rules(['required', 'uuid']),
                ])
                ->minItems(1)
                ->distinct()
                ->required()
                ->maxItems(fn(Get $get): ?int => $get('customer_flow_type') === 'corporate' ? null : 1)
                ->addable(fn(Get $get): bool => $get('customer_flow_type') === 'corporate')
                ->addActionLabel('Tambah PIC')
                ->visible(fn(Get $get) => filled($get('customer_flow_type')))
                ->saveRelationshipsUsing(function (Model $record, array $state): void {
                    // Filter out empty or null perorangan_id values
                    $selectedIds = array_filter(array_map(fn($item) => $item['perorangan_id'] ?? null, $state));

                    if (empty($selectedIds)) {
                        return; // Don't sync if no valid IDs
                    }

                    $peran = $record->corporate_id ? $record->corporate->nama : 'Pribadi';

                    // Sync dengan project dan simpan peran
                    $syncData = [];
                    foreach ($selectedIds as $id) {
                        if (!empty($id)) {
                            $syncData[$id] = ['peran' => $peran];
                        }
                    }

                    if (!empty($syncData)) {
                        $record->perorangan()->sync($syncData);
                    }

                    if ($record->corporate_id) {
                        $corporate = $record->corporate;

                        // Ambil semua ID PIC yang terhubung sebelumnya
                        $existingIds = $corporate->perorangan()->pluck('perorangan_id')->toArray();

                        // Tambahkan PIC baru yang belum terhubung
                        foreach ($selectedIds as $peroranganId) {
                            if (!in_array($peroranganId, $existingIds)) {
                                $corporate->perorangan()->attach($peroranganId, ['user_id' => auth()->id()]);
                            }
                        }

                        // Hapus PIC yang tidak ada di list sekarang
                        $toDetach = array_diff($existingIds, $selectedIds);
                        if (!empty($toDetach)) {
                            $corporate->perorangan()->detach($toDetach);
                        }
                    }
                }),
        ];
    }
}
