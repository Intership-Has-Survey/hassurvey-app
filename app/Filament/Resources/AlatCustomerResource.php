<?php

namespace App\Filament\Resources;

use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Corporate;
use App\Traits\GlolForms;
use App\Models\Perorangan;
use Filament\Tables\Table;
use App\Traits\GlobalForms;
use Filament\Pages\Actions;
use Illuminate\Database\Eloquent\Builder;
use App\Models\AlatCustomer;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use App\Filament\Resources\AlatCustomerResource\Pages\EditAlatCustomer;
use App\Filament\Resources\AlatCustomerResource\Pages\ListAlatCustomers;
use App\Filament\Resources\AlatCustomerResource\Pages\CreateAlatCustomer;
use App\Filament\Resources\AlatCustomerResource\RelationManagers\DetailKalibrasiRelationManager;

class AlatCustomerResource extends Resource
{
    use GlobalForms;
    protected static ?string $model = AlatCustomer::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Alat Customer';
    protected static ?string $navigationGroup = 'Customer';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {

        $uuid = request()->segment(2);
        return $form
            ->schema([
                Select::make('jenis_alat_id')
                    ->label('Jenis Alat')
                    ->relationship('jenisAlat', 'nama')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->validationMessages([
                        'required' => 'Jenis Alat wajib dipilih.',
                    ])
                    ->createOptionForm([
                        TextInput::make('nama')
                            ->label('Nama Jenis Alat')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->validationMessages([
                                'unique' => 'Nama alat ini sudah terdaftar, silakan gunakan yang lain.',
                            ]),
                        TextInput::make('keterangan')
                            ->label('Keterangan')
                            ->nullable(),
                    ]),
                TextInput::make('nomor_seri')
                    ->required()
                    ->label('Nomor Seri')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->validationMessages([
                        'unique' => 'Nomor seri ini sudah terdaftar, silakan gunakan yang lain.',
                    ])
                    ->required(),
                Select::make('merk_id')
                    ->relationship('merk', 'nama')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('nama')
                            ->label('Nama Merk')
                            ->required(),
                    ])
                    ->required()
                    ->validationMessages([
                        'required' => 'Merk wajib dipilih.',
                    ]),

                Select::make('kondisi')
                    ->label('Kondisi Alat')
                    ->required()
                    ->options([
                        true => 'Baik',
                        false => 'Dipakai',
                    ])
                    ->visibleOn('edit'),
                Textarea::make('keterangan')
                    ->nullable()
                    ->columnSpanFull(),
                Section::make('Informasi Customer')
                    ->schema([
                        Select::make('customer_flow_type')
                            ->label('Tipe Customer')
                            ->options(['perorangan' => 'Perorangan', 'corporate' => 'Corporate'])
                            ->live()->dehydrated(false)->native(false)->required()
                            ->afterStateUpdated(fn(Set $set) => $set('corporate_id', null))
                            ->validationMessages([
                                'required' => 'Customer tidak boleh kosong.',
                            ]),
                        Select::make('corporate_id')
                            ->relationship('corporate', 'nama')
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
                                        return Perorangan::whereNotIn('id', $selectedPicIds)->get()->mapWithKeys(fn($p) => [$p->id => "{$p->nama} - {$p->nik}"])->all();
                                    })
                                    ->searchable()
                                    ->createOptionForm(self::getPeroranganForm())
                                    ->createOptionUsing(fn(array $data): string => Perorangan::create($data)->id)
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Kolom Customer wajib diisi',
                                    ])
                                    ->rules(['required', 'uuid']),
                            ])
                            ->minItems(1)
                            ->validationMessages([
                                'required' => 'Kolom Customer wajib diisi'
                            ])
                            ->distinct()
                            ->required()
                            ->maxItems(fn(Get $get): ?int => $get('customer_flow_type') === 'corporate' ? null : 1)
                            ->addable(fn(Get $get): bool => $get('customer_flow_type') === 'corporate')
                            ->addActionLabel('Tambah PIC')
                            ->visible(fn(Get $get) => filled($get('customer_flow_type')))
                            ->saveRelationshipsUsing(function ($record, array $state): void {
                                $selectedIds = array_map(fn($item) => $item['perorangan_id'], $state);
                                $peran = $record->corporate_id ? $record->corporate->nama : 'Pribadi';

                                // Sync dengan project dan simpan peran
                                $syncData = [];
                                foreach ($selectedIds as $id) {
                                    $syncData[$id] = ['peran' => $peran];
                                }
                                $record->perorangan()->sync($syncData);

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
                            })
                    ]),

                Hidden::make('company_id')
                    ->default($uuid),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('corporate_id')
                    ->label('Customer')
                    ->getStateUsing(function ($record) {
                        return optional($record->corporate)->nama
                            ?? optional($record->perorangan)->nama
                            ?? 'Tidak ada customer';
                    }),
                TextColumn::make('jenisalat.nama')->label('Jenis Alat'),
                TextColumn::make('merk.nama')->label('Merek'),
                TextColumn::make('nomor_seri'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            DetailKalibrasiRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAlatCustomers::route('/'),
            'create' => CreateAlatCustomer::route('/create'),
            'edit' => EditAlatCustomer::route('/{record}/edit'),
        ];
    }
    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withTrashed();
    }
}
