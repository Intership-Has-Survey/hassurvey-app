<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Invoice')
                ->schema([
                    Forms\Components\TextInput::make('kode_invoice')
                        ->label('Kode Invoice')
                        ->default(function ($livewire) {
                            $parent = $livewire->ownerRecord;
                            return \App\Models\Invoice::generateKodeInvoiceFromModel($parent);
                        })
                        ->disabled()
                        ->dehydrated()
                        ->required(),
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft' => 'draft',
                            'terkirim' => 'terkirim',
                            'dibayar' => 'dibayar',
                            'lunas' => 'lunas',
                            'batal' => 'batal',
                        ])
                        ->required(),
                    Select::make('jenis')
                        ->label('Jenis Pembayaran')
                        ->options([
                            'DP' => 'DP',
                            'Termin 1' => 'Termin 1',
                            'Termin 2' => 'Termin 2',
                            'Pelunasan' => 'Pelunasan',
                        ])
                        ->required(),
                    TextInput::make('jumlah_pembayaran')
                        ->label('Jumlah Pembayaran (berapa persen dari total invoice)')
                        ->numeric()
                        ->required()
                        ->minValue(0),
                    TextInput::make('ppn')
                        ->label('PPN (%)')
                        ->numeric()
                        ->required()
                        ->minValue(0),
                    DatePicker::make('tanggal_mulai')
                        ->label('Tanggal Invoice dibuat')
                        ->native(false)
                        // ->default(today())
                        ->live(),
                    DatePicker::make('tanggal_selesai')
                        ->label('Tanggal Invoice jatuh tempo')
                        ->native(false)
                        // ->default(today())
                        ->live(),
                ])
                ->columns(2),
            Hidden::make('customer')
                ->default(fn(RelationManager $livewire) => $livewire->ownerRecord),
            Repeater::make('detailInvoices')
                ->relationship()
                ->columnSpanFull()
                ->label('Rincian Invoice')
                ->schema([
                    RichEditor::make('nama')
                        ->label('Nama Item')
                        ->required()
                        ->disableToolbarButtons([
                            'attachFiles',
                        ])
                        ->columnSpan(4),
                    TextInput::make('satuan')
                        ->label('Satuan')
                        ->dehydrated(true)
                        ->reactive(),

                    TextInput::make('jumlah')
                        ->label('Jumlah')
                        ->numeric(),

                    TextInput::make('harga')
                        ->dehydrated(true)
                        ->reactive(),

                    // TextInput::make('pajak')
                    //     ->dehydrated(true)
                    //     ->reactive()

                ])
                ->defaultItems(1)
                ->createItemButtonLabel('Tambah Rincian')
                ->columns(4),
            Hidden::make('company_id')
                ->default(fn() => \Filament\Facades\Filament::getTenant()?->getKey()),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('kode_invoice')->wrap(),
                Tables\Columns\TextColumn::make('tanggal_mulai')->label('Tanggal Invoice'),
                Tables\Columns\TextColumn::make('status')->label('Status'),
                Tables\Columns\TextColumn::make('jenis')->label('Tipe Invoice'),
                Tables\Columns\TextColumn::make('total_harga')->label('Total Harga')->money('idr', true),
                // Tables\Columns\TextColumn::make('project.kode_project')->label('Kode Project'),
                Tables\Columns\TextColumn::make('invoiceable')
                    ->label('Kode Project')
                    ->formatStateUsing(function ($record) {
                        if ($record->customer_type === \App\Models\Project::class) {
                            return $record->invoiceable->kode_project;
                        } else if ($record->customer_type === \App\Models\Sewa::class) {
                            return $record->invoiceable->kode_sewa;
                        } elseif ($record->customer_type === \App\Models\Kalibrasi::class) {
                            return $record->invoiceable->kode_kalibrasi;
                        } else {
                            return $record->invoiceable->kode_penjualan;
                        }

                        return '-';
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
