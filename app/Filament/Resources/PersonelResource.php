<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Personel;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PersonelResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PersonelResource\RelationManagers;

class PersonelResource extends Resource
{
    protected static ?string $model = Personel::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Personel';
    protected static ?string $navigationGroup = 'Manajemen Data Master';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->label('Nama Lengkap')
                    ->required(),
                TextInput::make('nik')
                    ->label('Nomor Induk Kependudukan (NIK)')
                    ->required(),
                Select::make('jabatan')
                    ->options([
                        'surveyor' => 'surveyor',
                        'asisten surveyor' => 'asisten surveyor',
                        'driver' => 'driver',
                        'drafter' => 'drafter',
                    ])
                    ->required()
                    ->native(false),
                Textarea::make('nomor_wa')
                    ->label('Nomor Whatsapps')
                    ->nullable(),
                Textarea::make('tanggal_lahir')
                    ->label('Tanggal Lahir')
                    ->nullable(),
                Textarea::make('provinsi')
                    ->label('Provinsi')
                    ->nullable(),
                Textarea::make('kota')
                    ->label('Kota/Kabupaten')
                    ->nullable(),
                Textarea::make('kecamatan')
                    ->label('Kecamatan')
                    ->nullable(),
                Textarea::make('desa')
                    ->label('Desa')
                    ->nullable(),
                Textarea::make('alamat')
                    ->label('Detail Alamat')
                    ->nullable(),
                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->nullable(),
                Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jenis_personel')
                    ->label('Jenis Personel')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nama_personel')
                    ->label('Nama Personel')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(
                        fn(string $state): string =>
                        str_contains($state, 'dalam project') ? 'warning' : 'success'
                    ),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Editor')
                    ->sortable()
                    ->searchable(),
            ])

            ->filters([

                SelectFilter::make('jenis_personel')
                    ->label('Jenis Personel')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Personel::query()
                            ->select('jenis_personel')
                            ->distinct()
                            ->pluck('jenis_personel', 'jenis_personel');
                    }),

                SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPersonels::route('/'),
            'create' => Pages\CreatePersonel::route('/create'),
            'edit' => Pages\EditPersonel::route('/{record}/edit'),
        ];
    }
}
