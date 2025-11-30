<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PicInternal;
use App\Traits\GlobalForms;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PicInternalResource\Pages;
use App\Filament\Resources\PicInternalResource\RelationManagers;

class PicInternalResource extends Resource
{
    use GlobalForms;
    protected static ?string $model = PicInternal::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';
    protected static ?string $navigationGroup = 'Manajemen Data Master';
    protected static ?int $navigationSort = 6;
    protected static ?string $pluralModelLabel = 'PIC Internal';

    public static function form(Form $form): Form
    {
        $uuid = request()->segment(2);
        return $form
            ->schema([
                Section::make('Informasi PIC Internal')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama PIC Internal')
                            ->required()
                            ->validationMessages([
                                'required' => 'Nama PIC Internal harus diisi'
                            ]),
                        TextInput::make('nik')
                            ->label('Nomor Induk Kependudukan (NIK)')
                            ->maxLength(16)
                            ->validationMessages([
                                'required' => 'NIK harus diisi'
                            ]),
                        TextInput::make('nomor_wa')
                            ->label('Nomor Handphone')
                            ->maxLength(15)
                            ->validationMessages([
                                'required' => 'Nomor Handphone harus diisi'
                            ]),
                        TextInput::make('email')
                            ->label('Email')
                    ])->columns(2),
                Section::make('Alamat PIC Internal')
                    ->schema(self::getAddressFields())->columns(2),
                Hidden::make('user_id')
                    ->default(auth()->id()),
                Hidden::make('company_id')
                    ->default($uuid),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('nama')->label('Nama PIC Internal')->searchable()->sortable(),
                TextColumn::make('nik')->label('NIK')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable()->sortable(),
                TextColumn::make('nomor_wa')->label('Nomor WA')->searchable()->sortable(),
                TextColumn::make('user.name')->label('Dibuat Oleh')->searchable()->sortable(),
            ])
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
            'index' => Pages\ListPicInternals::route('/'),
            'create' => Pages\CreatePicInternal::route('/create'),
            'edit' => Pages\EditPicInternal::route('/{record}/edit'),
        ];
    }
}
