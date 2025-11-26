<?php

namespace App\Traits;

use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\TrefRegion;
use Filament\Support\RawJs;
use Filament\Facades\Filament;
use App\Models\KategoriPengajuan;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\TextInput;

trait GlobalForms
{

    protected static function parseMoney($value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        // ambil hanya digit (buang Rp, spasi, titik, koma, dll)
        $clean = preg_replace('/[^\d]/', '', (string) $value);

        return (int) ($clean ?: 0);
    }

    private static function getCorporateForm(): array
    {
        return [
            Section::make('Informasi Perusahaan')
                ->schema([
                    TextInput::make('nama')
                        ->label('Nama Perusahaan')
                        ->maxLength(60)
                        ->required(),
                    TextInput::make('nib')
                        ->label('NIB')
                        ->maxLength(16)
                        ->minLength(15)
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
                        ->maxLength(254)
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
                        ->maxLength(25),
                ])->columns(2),

            Section::make('Alamat Perusahaan')
                ->schema(self::getAddressFields())
                ->columns(2),

            Hidden::make('user_id')
                ->default(auth()->id()),

            Hidden::make('company_id')
                ->default(fn() => Filament::getTenant()?->getKey()),

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
                        ->maxLength(254),
                    TextInput::make('nik')
                        ->label('Nomor Induk Kependudukan (NIK)')
                        ->length(length: 16)
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
                        ->maxLength(255)
                        ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule) {
                            $rule->where('company_id', Filament::getTenant()->id);
                            return $rule;
                        })
                        ->validationMessages([
                            'unique' => 'Email ini sudah terdaftar, silakan gunakan yang lain.',
                            'email' => 'Email tidak valid',
                        ])
                        ->email(),
                    TextInput::make('telepon')
                        ->label('Telepon')
                        ->tel()
                        ->maxLength(25),
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
                        ->maxLength(255),
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
                        ->maxLength(254)
                        ->validationMessages([
                            'unique' => 'Email sudah digunakan',
                            'required' => 'Email wajib diisi',
                            'regex' => 'Email tidak valid',
                        ]),
                    TextInput::make('telepon')
                        ->required()
                        ->label('Telepon')
                        ->tel()
                        ->maxLength(25)
                        ->validationMessages([
                            'required' => 'Telepon wajib diisi',
                            'regex' => 'Telepon tidak valid',
                        ]),
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
                        ->columnSpanFull()
                        ->maxLength(5000),
                    Select::make('hi')
                        ->label('Kategori Induk')
                        ->options(KategoriPengajuan::whereNull('parent_id')->pluck('nama', 'code'))
                        ->required()
                        ->reactive()
                        // ->dehydrated(false)
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->live()
                        // ->dehydrated(false)
                        ->createOptionForm([
                            TextInput::make('nama')
                                ->label('Nama Kategori Induk')
                                ->required()
                                ->maxLength(100),
                        ])
                        ->createOptionUsing(function (array $data) {
                            // Create kategori induk baru
                            $kategori = KategoriPengajuan::create([
                                'nama' => $data['nama'],
                                // Code akan di-generate otomatis oleh creating event (11, 12, 13, dst)
                            ]);

                            return $kategori->code;
                        })
                        ->afterStateUpdated(function (Set $set) {
                            $set('katpengajuan_id', null); // Reset subkategori ketika induk berubah
                        }),

                    // Select untuk Sub Kategori
                    Select::make('katpengajuan_id')
                        ->label('Sub Kategori')
                        ->options(function (Get $get) {
                            $parentCode = $get('hi');
                            if (!$parentCode) return [];

                            return KategoriPengajuan::where('code', 'like', $parentCode . '.%')
                                ->pluck('nama', 'code');
                        })
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->live()
                        ->visible(fn(callable $get) => !empty($get('hi')))
                        // ->dehydrate(true)
                        ->createOptionForm([
                            TextInput::make('nama')
                                ->label('Nama Sub Kategori')
                                ->required()
                                ->maxLength(100),
                        ])
                        ->createOptionUsing(function (array $data, Get $get) {
                            // Ambil parent_id dari select pertama
                            $parentCode = $get('hi');

                            if (!$parentCode) {
                                throw new \Exception('Silakan pilih kategori induk terlebih dahulu.');
                            }

                            // Cari parent berdasarkan code - PERBAIKAN: ambil ID-nya
                            $parent = KategoriPengajuan::where('code', $parentCode)->first();

                            if (!$parent) {
                                throw new \Exception('Kategori induk tidak ditemukan.');
                            }

                            // Create subkategori - PERBAIKAN: gunakan parent->id bukan parentCode
                            $subKategori = KategoriPengajuan::create([
                                'parent_id' => $parentCode, // <- INI PERBAIKAN UTAMA
                                'nama' => $data['nama'],
                                // Code akan di-generate otomatis oleh creating event (11.11, 11.12, dst)
                            ]);

                            return $subKategori->code;
                        })
                        ->required(),
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
                                ->maxLength(25)
                                ->required()
                                ->placeholder('Contoh: 1234567890')
                                ->validationMessages([
                                    'required' => 'Nomor rekening wajib diisi',
                                    'max_digits' => 'Tidak boleh lebih dari 25 digit',
                                ]),
                            TextInput::make('nama_pemilik')
                                ->label('Nama Pemilik')
                                ->required()
                                ->maxLength(255),

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
                        ->required()
                        ->maxLength(255),
                    TextInput::make('qty')
                        ->label('Jumlah')
                        ->numeric()
                        ->maxLength(4)
                        ->minValue(1)
                        ->default(1)
                        ->required()
                        ->validationMessages([
                            'required' => 'Jumlah wajib diisi',
                            'max_digits' => 'Jumlah tidak boleh lebih dari 12 digit',
                            'min_value' => 'Jumlah tidak boleh kurang dari 0',
                        ])
                        ->afterStateUpdated(function (Set $set, Get $get) {
                            $qty   = (int) $get('qty');
                            $harga = self::parseMoney($get('harga_satuan')); // harga sudah bersih
                            $set('total', $qty * $harga);
                        }),

                    TextInput::make('satuan')
                        ->placeholder('contoh : liter/kilogram/dll,...')
                        ->required()
                        ->maxLength(50),

                    TextInput::make('harga_satuan')
                        ->label('Harga Satuan')
                        ->numeric()
                        ->prefix('Rp ')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')

                        ->required()
                        ->minValue(0)
                        ->validationMessages([
                            'required' => 'Harga Satuan wajib diisi',
                            'max_digits' => 'Tidak boleh lebih dari 9 digit',
                            'min_value' => 'Tidak boleh kurang dari Rp 0'
                        ])
                        ->afterStateUpdated(function (Set $set, Get $get) {
                            $qty   = (int) $get('qty');
                            $harga = self::parseMoney($get('harga_satuan')); // harga sudah bersih
                            $set('total', $qty * $harga);
                        }),

                    Hidden::make('total')
                        ->dehydrated(true)
                        ->reactive()
                        ->default(fn(Get $get) => (int) $get('qty') * self::parseMoney($get('harga_satuan')))
                        ->afterStateHydrated(function (Set $set, Get $get) {
                            $set('total', (int) $get('qty') * self::parseMoney($get('harga_satuan')));
                        }),

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
                ->live()
                ->native(false)
                ->required()
                ->afterStateUpdated(function (Set $set, $state) {
                    if ($state === 'perorangan') {
                        $set('corporate_id', null);
                    }
                })
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

            Select::make('perorangan_single')
                ->label('Pilih Customer')
                ->relationship('perorangan', 'nama', fn($query) => $query->where('company_id', \Filament\Facades\Filament::getTenant()?->getKey()))
                ->getOptionLabelFromRecordUsing(fn(\App\Models\Perorangan $record) => "{$record->nama} - {$record->nik}")
                ->searchable()
                ->preload()
                ->createOptionForm(self::getPeroranganForm())
                ->createOptionUsing(fn(array $data): string => \App\Models\Perorangan::create($data)->id)
                ->visible(fn(Get $get) => $get('customer_flow_type') === 'perorangan')
                ->required(fn(Get $get) => $get('customer_flow_type') === 'perorangan')
                ->saveRelationshipsUsing(function ($state, $record) {
                    if ($state && is_string($state)) {
                        $record->perorangan()->sync([
                            $state => ['peran' => 'Pribadi'],
                        ]);
                    } elseif ($state && is_array($state)) {
                        // Handle array case
                        $syncData = [];
                        foreach ($state as $id) {
                            if (!empty($id)) {
                                $syncData[$id] = ['peran' => 'Pribadi'];
                            }
                        }
                        if (!empty($syncData)) {
                            $record->perorangan()->sync($syncData);
                        }
                    }
                })
                ->afterStateUpdated(function (callable $set, $state) {
                    if ($state) {
                        $set('perorangan', [['perorangan_id' => $state]]);
                    } else {
                        $set('perorangan', []);
                    }
                })
                ->validationMessages([
                    'required' => 'Kolom Customer wajib diisi',
                ]),

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
                        ->visible(fn(Get $get) => filled($get('customer_flow_type') === 'corporate'))
                        ->validationMessages([
                            'required' => 'Kolom Customer wajib diisi',
                        ])
                        ->rules(['required', 'uuid']),
                ])
                ->minItems(1)
                ->distinct()
                ->required()
                ->validationMessages([
                    'required' => 'PIC tidak boleh kosong',
                ])
                ->maxItems(fn(Get $get): ?int => $get('customer_flow_type') === 'corporate' ? null : 1)
                ->addable(fn(Get $get): bool => $get('customer_flow_type') === 'corporate')
                ->addActionLabel('Tambah PIC')
                // ->visible(fn(Get $get) => filled($get('customer_flow_type') === 'corporate'))
                ->visible(fn(Get $get) => $get('customer_flow_type') === 'corporate')
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

    private static function getPeroranganOptions($excludeIds = []): array
    {
        return \App\Models\Perorangan::where('company_id', \Filament\Facades\Filament::getTenant()?->getKey())
            ->when(!empty($excludeIds), fn($q) => $q->whereNotIn('id', $excludeIds))
            ->get()
            ->mapWithKeys(fn($p) => [$p->id => "{$p->nama} - {$p->nik}"])
            ->all();
    }
}
