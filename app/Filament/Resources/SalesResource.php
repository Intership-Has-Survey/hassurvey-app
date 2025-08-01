<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Sales;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\TrefRegion;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SalesResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SalesResource\RelationManagers;

class SalesResource extends Resource
{
    protected static ?string $model = Sales::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Manajemen Data Master';
    protected static ?int $navigationSort = 5;
    protected static ?string $pluralModelLabel = 'Sales';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Sales')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Sales')
                            ->required()
                            ->maxLength(50),
                        TextInput::make('nik')
                            ->label('NIK')
                            ->required()
                            ->maxLength(16)
                            ->unique(ignoreRecord: true),
                        TextInput::make('email')
                            ->label('Email')
                            ->required()
                            ->maxLength(50),
                        TextInput::make('telepon')
                            ->label('Telepon')
                            ->required()
                            ->maxLength(50),
                    ])->columns(2),
                Section::make('Alamat')
                    ->schema(self::getAddressFields())->columns(2),
                Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama'),
                TextColumn::make('email'),
                TextColumn::make('telepon'),
                TextColumn::make('user.name')
                    ->label('Dibuat Oleh')
                    ->sortable()
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function ($record) {
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.sales', ['record' => $record]);

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, 'sales-' . $record->id . '.pdf');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Sales Terdaftar')
            ->emptyStateDescription('Silahkan buat data sales baru untuk memulai.')
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSales::route('/create'),
            'edit' => Pages\EditSales::route('/{record}/edit'),
        ];
    }
}
