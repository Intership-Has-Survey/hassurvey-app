<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DaftarAlatResource\Pages;
use App\Models\DaftarAlat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;

class DaftarAlatResource extends Resource
{
    protected static ?string $model = DaftarAlat::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench';

    protected static ?string $navigationLabel = 'Daftar Alat';

    protected static ?string $navigationGroup = 'Manajemen Data Master';

    protected static ?int $navigationSort = 1;

    protected static ?int $navigationGroupSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('jenis_alat')
                    ->options([
                        'GPS' => 'GPS',
                        'Drone' => 'Drone',
                        'OTS' => 'OTS',
                    ]),
                Forms\Components\TextInput::make('nomor_seri')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('merk')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('pemilik_id')
                    ->relationship('pemilik', 'nama')
                    ->searchable()
                    // Menambahkan form modal untuk membuat pemilik baru
                    ->createOptionForm([
                        TextInput::make('nama')
                            ->label('Nama Pemilik')
                            ->required(),
                        TextInput::make('NIK')
                            ->minLength(16)
                            ->maxLength(16)
                            ->required(),
                        TextInput::make('email')
                            ->required()
                            ->email(),
                        TextInput::make('telepon')
                            ->required()
                            ->label('No. Telp'),
                        TextInput::make('alamat')
                            ->required()
                            ->label('Alamat'),
                        Hidden::make('user_id')
                            ->default(auth()->id()),
                    ])
                    ->preload()
                    ->required(),
                Forms\Components\Textarea::make('keterangan')
                    ->nullable()
                    ->columnSpanFull(),

                Forms\Components\Select::make('kondisi')
                    ->label('Kondisi Alat')
                    ->required()
                    ->options([
                        true => 'Baik',
                        false => 'Bermasalah',
                    ])
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_seri')->searchable(),
                Tables\Columns\TextColumn::make('jenis_alat')->searchable(),
                Tables\Columns\TextColumn::make('merk')->searchable(),
                Tables\Columns\TextColumn::make('pemilik.nama')->searchable()->sortable(),

                BadgeColumn::make('kondisi')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Baik' : 'Bermasalah')
                    ->color(fn(bool $state): string => match ($state) {
                        true => 'success',
                        false => 'danger',
                    }),

                BadgeColumn::make('status')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Tersedia' : 'Tidak Tersedia')
                    ->color(fn(bool $state): string => match ($state) {
                        true => 'success',
                        false => 'warning',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d-m-Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('jenis_alat')
                    ->options([
                        'GPS' => 'GPS',
                        'Drone' => 'Drone',
                        'OTS' => 'OTS',
                    ]),
                TernaryFilter::make('kondisi')
                    ->label('Kondisi')
                    ->placeholder('Semua Kondisi')
                    ->trueLabel('Baik')
                    ->falseLabel('Bermasalah'),

                TernaryFilter::make('status')
                    ->label('Ketersediaan')
                    ->placeholder('Semua Status')
                    ->trueLabel('Tersedia')
                    ->falseLabel('Tidak Tersedia'),
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
            'index' => Pages\ListDaftarAlats::route('/'),
            'create' => Pages\CreateDaftarAlat::route('/create'),
            'edit' => Pages\EditDaftarAlat::route('/{record}/edit'),
        ];
    }
}
