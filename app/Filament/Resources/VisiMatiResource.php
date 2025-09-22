<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisiMatiResource\Pages;
use App\Filament\Resources\VisiMatiResource\RelationManagers\KewajibanBayarsRelationManager;
use App\Filament\Resources\VisiMatiResource\RelationManagers\PemasukansRelationManager;
use App\Filament\Resources\VisiMatiResource\RelationManagers\PenerimaOperasionalsRelationManager;
use App\Filament\Resources\VisiMatiResource\RelationManagers\PengeluaransRelationManager;
use App\Models\VisiMati;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VisiMatiResource extends Resource
{
    protected static ?string $model = VisiMati::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Detail Utama')
                ->schema([
                    TextInput::make('nama')->required()->maxLength(255),
                    Textarea::make('deskripsi')->nullable(),
                ]),

            Section::make('Sub-Kategori')
                ->schema([
                    CheckboxList::make('sub_kategori')
                        ->label('Pilih Sub-Kategori')
                        ->options([
                            'tabungan' => 'Tabungan',
                            'operasional' => 'Operasional',
                        ])
                        ->minItems(1)
                        ->live()
                        ->disabledOn('edit'),
                ]),

            Section::make('Detail Tabungan')
                ->schema([
                    TextInput::make('tabungan.nama')
                        ->label('Nama Tabungan')
                        ->required(fn(Get $get): bool => in_array('tabungan', $get('sub_kategori') ?? [])),
                    TextInput::make('tabungan.target_nominal')
                        ->label('Target Nominal')
                        ->numeric()
                        ->required(fn(Get $get): bool => in_array('tabungan', $get('sub_kategori') ?? [])),
                    Select::make('tabungan.target_tipe')
                        ->label('Target Tipe')
                        ->options([
                            'orang' => 'Orang',
                            'bangunan' => 'Bangunan',
                        ])
                        ->required(fn(Get $get): bool => in_array('tabungan', $get('sub_kategori') ?? [])),
                ])
                ->visible(fn(Get $get): bool => in_array('tabungan', $get('sub_kategori') ?? [])),

            Section::make('Detail Operasional')
                ->schema([
                    TextInput::make('operasional.nama')
                        ->label('Nama Operasional')
                        ->required(fn(Get $get): bool => in_array('operasional', $get('sub_kategori') ?? [])),
                ])
                ->visible(fn(Get $get): bool => in_array('operasional', $get('sub_kategori') ?? [])),
            Hidden::make('company_id')
                ->default(fn() => \Filament\Facades\Filament::getTenant()?->getKey()),
        ]);

    }

    public static function getRelationManagers(): array
    {
        return [
            // Relation untuk Tabungan
            \Filament\Resources\RelationManagers\RelationGroup::make('Tabungan Relations', [
                PemasukansRelationManager::class,
                PengeluaransRelationManager::class,
            ])
                ->when(fn($record) => in_array('tabungan', $record?->sub_kategori ?? [])),

            // Relation untuk Operasional
            \Filament\Resources\RelationManagers\RelationGroup::make('Operasional Relations', [
                KewajibanBayarsRelationManager::class,
                PenerimaOperasionalsRelationManager::class,
            ])
                ->when(fn($record) => in_array('operasional', $record?->sub_kategori ?? [])),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('deskripsi')->limit(50),
            ])
            ->filters([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisiMatis::route('/'),
            'create' => Pages\CreateVisiMati::route('/create'),
            'edit' => Pages\EditVisiMati::route('/{record}/edit'),
        ];
    }
}
