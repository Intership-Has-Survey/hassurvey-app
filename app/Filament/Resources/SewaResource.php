<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SewaResource\Pages;
use App\Filament\Resources\SewaResource\RelationManagers;
use App\Models\Sewa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;

class SewaResource extends Resource
{
    protected static ?string $model = Sewa::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Penyewaan Alat';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('jenis')
                    ->options([
                        'sewa keluar' => 'Sewa Keluar',
                        'sewa untuk proyek' => 'Sewa untuk Proyek',
                    ]),
                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->label('Judul Penyewaan'),
                Forms\Components\Select::make('customer_id')
                    ->relationship('customer', 'nama')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Nama Klien/Customer')
                    ->createOptionForm([
                        TextInput::make('nama')
                            ->label('Nama Klien/Perusahaan')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('telepon')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Textinput::make('alamat')
                            ->required()
                            ->columnSpanFull(),
                        Hidden::make('user_id')
                            ->default(auth()->id()),
                    ]),
                Forms\Components\DatePicker::make('tgl_mulai')
                    ->required(),
                Forms\Components\DatePicker::make('tgl_selesai')
                    ->required(),
                Forms\Components\TextInput::make('lokasi')
                    ->required(),
                Forms\Components\TextInput::make('alamat')
                    ->required(),
                Forms\Components\TextInput::make('total_biaya')
                    ->prefix('Rp')
                    ->numeric()
                    ->nullable(),
                Hidden::make('user_id')
                    ->default(auth()->id()),
            ])->columns(2);;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis Sewa')
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'sewa keluar' => 'warning',
                        'untuk project' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul Penyewaan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.nama')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_mulai')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_selesai')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lokasi')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_biaya')
                    ->numeric()
                    ->prefix('Rp')
                    ->sortable(),
            ])->filters([
                //
            ])->actions([
                Tables\Actions\ViewAction::make(),
            ])->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])
            ])->headerActions([
                //
            ])
            ->filters([
                TrashedFilter::make(),
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
            RelationManagers\RiwayatSewasRelationManager::class,
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSewa::route('/'),
            'create' => Pages\CreateSewa::route('/create'),
            'edit' => Pages\EditSewa::route('/{record}/edit'),
        ];
    }
}
