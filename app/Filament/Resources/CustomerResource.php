<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CustomerResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CustomerResource\RelationManagers;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Manajemen Data Master';
    protected static ?string $recordTitleAttribute = 'nama_pic';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama_pic')
                    ->label('Nama Customer')
                    ->required()
                    ->maxLength(255),
                Select::make('tipe_customer')
                    ->options([
                        'Perorangan' => 'Perorangan',
                        'Perusahaan' => 'Perusahaan',
                        'Instansi Pemerintah' => 'Instansi Pemerintah',
                    ])
                    ->required()
                    ->native(false),
                TextInput::make('nama_institusi')
                    ->label('Nama Perusahaan/Institusi')
                    ->maxLength(255)
                    ->placeholder('Kosongkan jika Perorangan'),
                TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                TextInput::make('telepon')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Textarea::make('alamat')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('user_id')
                    ->label('User')
                    ->required()
                    ->readOnly()
                    ->hint('tidak perlu diisi')
                    ->default(auth()->user()->id),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_pic')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipe_customer')
                    ->badge(),
                Tables\Columns\TextColumn::make('nama_institusi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')->label('Editor'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([


                // SelectFilter::make('jenis_personel')
                //     ->label('Jenis Personel')
                //     ->searchable()
                //     ->options(function () {
                //         return Personel::query()
                //             ->select('jenis_personel')
                //             ->distinct()
                //             ->pluck('jenis_personel', 'jenis_personel');
                //     }),

                SelectFilter::make('tipe_customer')
                    ->label('Tipe')
                    // ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('nama_institusi')
                    ->label('Nama Institusi')
                    // ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            // Jika Anda ingin menampilkan daftar proyek milik customer di halaman detail,
            // buat Relation Manager untuk Project.
            // RelationManagers\ProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
