<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DaftarAlatResource\Pages;
use App\Filament\Resources\DaftarAlatResource\RelationManagers;
use App\Models\DaftarAlat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Filters\SelectFilter;




class DaftarAlatResource extends Resource
{
    protected static ?string $model = DaftarAlat::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench';

    protected static ?string $navigationLabel = 'Daftar Alat';

    protected static ?string $navigationGroup = 'Manajemen Data Master';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_alat')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('jenis_alat')
                    ->options([
                        'GPS' => 'GPS',
                        'Drone' => 'Drone',
                        'OTS' => 'OTS',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('merk')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('kondisi')
                    ->options([
                        'Baik' => 'Baik',
                        'Bermalasah' => 'Bermasalah',
                    ])
                    ->default('Baik')
                    ->required(),
                Forms\Components\Textarea::make('keterangan')
                    ->nullable()
                    ->maxLength(65535),
                Hidden::make('user_id')
                    ->default(auth()->id())
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_alat')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis_alat')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('merk')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kondisi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'Tersedia' => 'success',
                        'Tidak Tersedia' => 'danger',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('jenis_alat')
                    ->label('Jenis Alat')
                    ->options([
                        'GPS' => 'GPS',
                        'Drone' => 'Drone',
                        'OTS' => 'OTS',
                    ])
                    ->multiple(), // Opsional: Hapus ini jika hanya ingin memilih satu jenis

                // Filter untuk Status Ketersediaan
                SelectFilter::make('status')
                    ->label('Status Ketersediaan')
                    ->options([
                        'Tersedia' => 'Tersedia',
                        'Tidak Tersedia' => 'Tidak Tersedia',
                    ])
                    ->multiple(), // Opsional: Hapus ini jika hanya ingin memilih satu status
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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id(); // atau Auth::id()
        $data['status'] = 'Tersedia'; // Set default status saat membuat alat baru
        return $data;
    }
    public static function getRelations(): array
    {
        return [

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
