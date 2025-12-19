<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\KategoriPengajuan;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KategoriPengajuanResource\Pages;
use App\Filament\Resources\KategoriPengajuanResource\RelationManagers;

class KategoriPengajuanResource extends Resource
{
    protected static ?string $model = KategoriPengajuan::class;
    // protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    //agar tidak terpengaruh tenant
    protected static bool $isScopedToTenant = false;
    protected static ?string $navigationLabel = 'Kategori Pengajuan/Berita Acara';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Forms\Components\TextInput::make('code')
                    ->label('Kode Kategori Pengajuan')
                    ->disabled() // biar user tidak bisa ubah manual
                    ->dehydrated(false) // jangan simpan input dari user
                    ->visibleOn(['edit', 'view'])
                    ->columnSpan(2),
                Forms\Components\Select::make('parent_id')
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
                        Forms\Components\TextInput::make('nama')
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
                // Forms\Components\TextInput::make('parent_id')
                //     ->label('Kategori Induk') // biar user tidak bisa ubah manual
                //     // ->dehydrated(false) // jangan simpan input dari user
                //     // ->visibleOn(['edit', 'view'])
                //     ->maxLength(5),
                Forms\Components\TextInput::make('nama')
                    ->label('Sub Kategori')
                    ->maxLength(100)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('code', 'asc')
            ->columns([
                //
                TextColumn::make('code')
                    ->label('Kode')
                    ->sortable()
                    ->searchable(),

                // TextColumn::make('nama')
                //     ->label('Induk Kategori')
                //     ->state(
                //         fn($record) =>
                //         $record->parent_id
                //             ? optional($record->parentKategori)->nama
                //             : $record->nama
                //     )
                //     ->weight(
                //         fn($record) =>
                //         $record->parent_id ? 'normal' : 'bold'
                //     )->sortable()
                //     ->searchable(),

                // TextColumn::make('sub')
                //     ->label('Sub Kategori')
                //     ->state(function ($record) {
                //         return $record->parent_id
                //             ? $record->nama
                //             : '-';
                //     })
                //     ->sortable()
                //     ->searchable(),


                TextColumn::make('parentKategori.nama')
                    ->label('Kategori Induk')
                    ->sortable()
                    ->searchable()
                    ->state(
                        fn($record) =>
                        $record->parent_id
                            ? optional($record->parentKategori)->nama // sub → tampil induk
                            : $record->nama                            // induk → tampil diri sendiri
                    )->weight(
                        fn($record) =>
                        $record->parent_id ? 'normal' : 'bold'
                    ),

                TextColumn::make('nama')
                    ->label('Nama Kategori')
                    ->sortable()
                    ->searchable()
                    ->weight(
                        fn($record) =>
                        $record->parent_id ? 'normal' : 'bold'
                    ),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diubah Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKategoriPengajuans::route('/'),
            'create' => Pages\CreateKategoriPengajuan::route('/create'),
            'edit' => Pages\EditKategoriPengajuan::route('/{record}/edit'),
            'custom' => Pages\CobaPage::route('/kategori-pengajuan-setting'),
        ];
    }
}
