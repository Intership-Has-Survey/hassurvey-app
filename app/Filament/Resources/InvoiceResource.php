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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Invoice')
                ->schema([
                    TextInput::make('kode_invoice')
                        ->label('Kode Invoice')
                        ->columnSpan(2),
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
            // Section::make('Informasi Customer')
            //     ->schema(self::getCustomerForm()),
            Section::make('Informasi Pelanggan')
                ->schema([
                    Select::make('customer')
                        ->label('Pelangan')
                        ->options(function () {
                            // Ambil data corporate
                            $corporate = DB::table('corporate')
                                ->select('id', 'nama')
                                ->get()
                                ->mapWithKeys(fn($item) => [$item->id => "Corporate: {$item->nama}"]);

                            // Ambil data perorangan
                            $perorangan = DB::table('perorangan')
                                ->select('id', 'nama')
                                ->get()
                                ->mapWithKeys(fn($item) => [$item->id => "Perorangan: {$item->nama}"]);

                            // Gabungkan
                            return $corporate->merge($perorangan)->toArray();
                        })
                        ->required(),
                    TextInput::make('telepon'),
                    TextInput::make('email'),
                ]),
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

                    TextInput::make('jumlah')
                        ->label('Jumlah')
                        ->numeric()
                        ->prefix('Rp ')
                        ->stripCharacters(',')

                        ->required()
                        ->minValue(0)
                        ->validationMessages([
                            'required' => 'Harga Satuan wajib diisi',
                            'max_digits' => 'Tidak boleh lebih dari 9 digit',
                            'min_value' => 'Tidak boleh kurang dari Rp 0'
                        ]),

                    TextInput::make('harga')
                        ->dehydrated(true)
                        ->reactive(),
                    TextInput::make('diskon')
                        ->dehydrated(true)
                        ->reactive(),
                    TextInput::make('pajak')
                        ->dehydrated(true)
                        ->reactive()

                ])
                ->defaultItems(1)
                ->createItemButtonLabel('Tambah Rincian')
                ->columns(4),

            // ])->columns(2),
            // Hidden::make('user_id')->default(auth()->id()),
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
                        'batal' => 'batal',
                        default => 'secondary',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('invoicepreview')
                    ->label('Preview Invoice')
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
        ];
    }
}
