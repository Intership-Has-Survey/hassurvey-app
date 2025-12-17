<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Penawaran;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PenawaranResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PenawaranResource\RelationManagers;

class PenawaranResource extends Resource
{
    protected static ?string $model = Penawaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Penawaran';

    protected static ?string $tenantRelationshipName = 'Penawarans';

    protected static ?int $navigationSort = 6;

    protected static ?string $pluralModelLabel = 'Penawaran';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Informasi Penawaran')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('kode_penawaran')
                            ->label('Kode Penawaran')
                            ->default(function (callable $get) {
                                return \App\Models\Penawaran::generateKodePenawaranFromModel();
                            })
                            ->columnSpanFull()
                            ->readOnly()
                            ->disabledOn('edit'),
                        Select::make('customer_type')
                            ->label('Tipe Customer')
                            ->options([
                                'App\Models\Corporate' => 'Corporate',
                                'App\Models\Perorangan' => 'Perorangan',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set) {
                                $set('customer_id', null);
                            }),
                        Forms\Components\Select::make('customer_id')
                            ->label('ID Customer')
                            ->required()
                            ->options(function (callable $get) {
                                $type = $get('customer_type');
                                if ($type === 'App\Models\Corporate') {
                                    return \App\Models\Corporate::pluck('nama', 'id');
                                } elseif ($type === 'App\Models\Perorangan') {
                                    return \App\Models\Perorangan::pluck('nama', 'id');
                                }
                                return [];
                            })
                            ->searchable()
                            ->reactive(),
                        Select::make('status')
                            ->label('Status Penawaran')
                            ->options([
                                'Draft' => 'Draft',
                                'Terkirim' => 'Terkirim',
                                'Diterima' => 'Diterima',
                                'Ditolak' => 'Ditolak',
                            ])
                            ->required(),
                        DatePicker::make('tanggal')
                            ->label('Tanggal Penawaran')
                            ->required()
                            ->native(false),
                    ]),
                Section::make('Detail Penawaran')
                    ->schema([
                        Repeater::make('detailPenawarans')
                            ->label('Detail Penawaran')
                            ->relationship()
                            ->schema([
                                Forms\Components\RichEditor::make('nama')
                                    ->label('Deskripsi')
                                    ->required()
                                    ->disableToolbarButtons([
                                        'attachFiles',
                                        'blockquote',
                                        'codeBlock',
                                        'h2',
                                        'h3',
                                        'link',
                                        'strike',
                                        'underline',
                                    ])
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('jumlah')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('harga')
                                    ->label('Harga Satuan')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('satuan')
                                    ->label('Jenis Satuan')
                                    ->placeholder('contoh: pcs, unit, paket')
                                    ->required(),
                            ])
                            ->columns(3)
                            ->required()
                            // ->columnSpan(12)
                            ->createItemButtonLabel('Tambah Rincian'),
                    ]),
                Hidden::make('company_id')
                    ->default(fn() => \Filament\Facades\Filament::getTenant()?->getKey()),
                // ->default(fn() => session('company_id')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('kode_penawaran')->wrap(),
                Tables\Columns\TextColumn::make('tanggal')->date(),
                Tables\Columns\TextColumn::make('status')->wrap(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('penawaranpreview')
                    ->label('Print')
                    ->url(fn($record) => route('penawaran', [
                        'company' => $record->company_id,
                        'penawaranId' => $record->id,
                    ]))
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-eye')
                    ->color('secondary'),
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
            'index' => Pages\ListPenawarans::route('/'),
            'create' => Pages\CreatePenawaran::route('/create'),
            'edit' => Pages\EditPenawaran::route('/{record}/edit'),
            // 'custom' => Pages\PenawaranSetting::route('/penawaran-setting'),
        ];
    }
}
