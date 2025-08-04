<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Kalibrasi;
use App\Models\Perorangan;
use Filament\Tables\Table;
use App\Traits\GlobalForms;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\KalibrasiResource\Pages;
use App\Filament\Resources\KalibrasiResource\RelationManagers\PengajuanDanasRelationManager;
use App\Filament\Resources\KalibrasiResource\RelationManagers\DetailKalibrasiRelationManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class KalibrasiResource extends Resource
{
    use GlobalForms;
    protected static ?string $model = Kalibrasi::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Kalibrasi';
    protected static ?string $navigationGroup = 'Layanan';
    protected static ?string $pluralModelLabel = 'Jasa Kalibrasi';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Customer')
                    ->schema([
                        Select::make('customer_flow_type')
                            ->label('Tipe Customer')
                            ->options(['perorangan' => 'Perorangan', 'corporate' => 'Corporate'])
                            ->live()->dehydrated(false)->native(false)
                            ->afterStateUpdated(fn(Set $set) => $set('corporate_id', null)),

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
                                    ->createOptionUsing(fn(array $data): string => Perorangan::create($data)->id),
                            ])
                            ->minItems(1)
                            ->distinct()
                            ->maxItems(fn(Get $get): ?int => $get('customer_flow_type') === 'corporate' ? null : 1)
                            ->addable(fn(Get $get): bool => $get('customer_flow_type') === 'corporate')
                            ->addActionLabel('Tambah PIC')
                            ->visible(fn(Get $get) => filled($get('customer_flow_type')))
                            ->saveRelationshipsUsing(function (Model $record, array $state): void {
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
                Section::make('Informasi Kalibrasi')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Kalibrasi')
                            ->required(),
                        TextInput::make('harga')
                            ->label('Harga')
                            ->numeric()
                            ->prefix('Rp ')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(','),
                        Select::make('status')
                            ->options([
                                'dalam_proses' => 'Dalam proses',
                                'selesai' => 'Selesai'
                            ])
                            ->visibleOn('edit')
                            ->default('dalam_proses')
                            ->native(false),
                    ]),
            ]);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        // Simpan relasi many-to-many dengan peran
        if (isset($data['customer_flow_type']) && $data['customer_flow_type'] === 'perorangan' && isset($data['perorangan_id'])) {
            $data['perorangan_ids'] = [$data['perorangan_id']];
            unset($data['perorangan_id']);
        }

        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        // Simpan relasi many-to-many dengan peran
        if (isset($data['customer_flow_type']) && $data['customer_flow_type'] === 'perorangan' && isset($data['perorangan_id'])) {
            $data['perorangan_ids'] = [$data['perorangan_id']];
            unset($data['perorangan_id']);
        }

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')->sortable()->searchable()->wrap(),
                Tables\Columns\TextColumn::make('customer_display')
                    ->label('Klien Utama')
                    ->state(function (Kalibrasi $record): string {
                        if ($record->corporate) {
                            return $record->corporate->nama;
                        }
                        return $record->perorangan->first()?->nama ?? 'N/A';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->whereHas('corporate', fn($q) => $q->where('nama', 'like', "%{$search}%"))
                            ->orWhereHas('perorangan', fn($q) => $q->where('nama', 'like', "%{$search}%"));
                    }),
                Tables\Columns\TextColumn::make('perorangan.nama')
                    ->label('PIC')
                    ->listWithLineBreaks()
                    ->limitList(2),
                Tables\Columns\TextColumn::make('nama'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'dalam_proses' => 'primary',
                        'selesai' => 'success',
                        default => 'primary'
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
        ;
    }

    public static function getRelations(): array
    {
        return [
            PengajuanDanasRelationManager::class,
            DetailKalibrasiRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKalibrasis::route('/'),
            'create' => Pages\CreateKalibrasi::route('/create'),
            'edit' => Pages\EditKalibrasi::route('/{record}/edit'),
        ];
    }
}
