<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Invoice;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Traits\GlobalForms;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\InvoiceResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\InvoiceResource\RelationManagers;

class InvoiceResource extends Resource
{
    use GlobalForms;
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Invoice';

    protected static ?string $tenantRelationshipName = 'invoices';

    protected static ?int $navigationSort = 5;

    protected static ?string $pluralModelLabel = 'Invoice';

    public static function form(Form $form): Form
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
                        ->required()
                        ->columnSpanFull(),
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
                        ->label('PPN (%), beri 0 jika tidak ada')
                        ->numeric()
                        ->required()
                        ->minValue(0),
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
                            'blockquote',
                            'codeBlock',
                            'h2',
                            'h3',
                            'link',
                            'strike',
                            'underline',
                        ])
                        ->columnSpanFull(),
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
                ->columns(3),
            Hidden::make('company_id')
                ->default(fn() => \Filament\Facades\Filament::getTenant()?->getKey()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //->columns([
                TextColumn::make('kode_invoice')->wrap()
                    ->label('Kode Invoice')
                    ->searchable(),
                TextColumn::make('tanggal_mulai')
                    ->label('Tanggal Dibuat')
                    ->date('d-m-Y')
                    ->sortable(),
                TextColumn::make('tanggal_selesai')
                    ->label('Jatuh Tempo')
                    ->date('d-m-Y')
                    ->sortable(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->color(fn(string $state): string => match ($state) {
                        'dibayar' => 'success',
                        'draft' => 'primary',
                        'terkirim' => 'info',
                        'batal' => 'danger',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('customer_type')
                    ->label('Kode Layanan')
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
                    })
                    ->url(function ($record) {
                        // $model = $record->invoiceable;

                        if (!$record) return null; // hindari error jika data tidak ada

                        // Tentukan resource berdasarkan model
                        return match ($record->customer_type) {
                            \App\Models\Project::class   => \App\Filament\Resources\ProjectResource::getUrl('view', ['record' => $record->invoiceable->id]),
                            \App\Models\Sewa::class      => \App\Filament\Resources\SewaResource::getUrl('view', ['record' => $record->invoiceable->id]),
                            \App\Models\Kalibrasi::class => \App\Filament\Resources\KalibrasiResource::getUrl('view', ['record' => $record->invoiceable->id]),
                            \App\Models\Penjualan::class => \App\Filament\Resources\PenjualanResource::getUrl('view', ['record' => $record->invoiceable->id]),
                            default => null,
                        };
                    })
                    ->openUrlInNewTab(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('invoicepreview')
                    ->label('Print')
                    ->url(fn($record) => route('invoice', [
                        'company' => $record->company_id,
                        'invoice' => $record->id,
                    ]))
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
            'custom' => Pages\InvoiceSetting::route('/invoice-setting'),
        ];
    }
}
