<?php

namespace App\Filament\Resources\KalibrasiResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class StatusPembayaranRelationManager extends RelationManager
{

    protected static string $relationship = 'statusPembayaran';

    protected static ?string $title = 'Riwayat Pembayaran';

    protected static bool $isLazy = false;

    public function form(Form $form): Form
    {
        $kalibrasi = $this->ownerRecord;
        $nilai = (float) str_replace(['Rp ', '.'], '', $kalibrasi->harga);
        $totalDibayar = (float) $kalibrasi->statusPembayaran()->sum('nilai');
        $sisaPembayaran = $nilai - $totalDibayar;

        return $form
            ->schema([
                Forms\Components\Placeholder::make('sisa_tagihan')
                    ->label('Sisa Pembayaran yang Belum Dilunasi')
                    ->content(function () use ($sisaPembayaran) {
                        if ($sisaPembayaran <= 0) {
                            return 'Lunas';
                        }
                        return 'Rp ' . number_format($sisaPembayaran, 0, ',', '.');
                    })
                    ->visibleOn('create'),
                Select::make('nama_pembayaran')
                    ->label('Metode Pembayaran')
                    ->options([
                        'Transfer Bank' => 'Transfer Bank',
                        'Tunai' => 'Tunai',
                        'Lainnya' => 'Lainnya',
                    ])
                    ->required()
                    ->validationMessages([
                        'required' => 'Metode pembayaran wajib diisi',
                    ])
                    ->native(false),

                Select::make('jenis_pembayaran')
                    ->options([
                        'DP' => 'DP',
                        'Pelunasan' => 'Pelunasan',
                        'Termin 1' => 'Termin 1',
                        'Termin 2' => 'Termin 2',
                    ])
                    ->required()
                    ->validationMessages([
                        'required' => 'Jenis pembayaran wajib diisi',
                    ])
                    ->native(false),

                TextInput::make('nilai')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->required()
                    ->validationMessages([
                        'required' => 'Nilai wajib diisi',
                    ])
                    ->prefix('Rp')
                    ->maxlength(20),

                FileUpload::make('bukti_pembayaran_path')
                    ->label('Bukti Pembayaran')
                    ->image()
                    ->maxSize(5120)
                    ->required()
                    ->validationMessages([
                        'required' => 'Bukti Pembayaran wajib diisi',
                    ])
                    ->disk('public')
                    ->directory('bukti-pembayaran')
                    ->columnSpanFull(),

                Hidden::make('user_id')
                    ->default(auth()->id()),

                Hidden::make('payable_id')
                    ->default(fn() => $this->ownerRecord->id),

                Hidden::make('payable_type')
                    ->default(fn() => get_class($this->ownerRecord)),

                Hidden::make('company_id')
                    ->default(fn() => $this->ownerRecord->company_id),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_pembayaran')
            ->columns([
                TextColumn::make('nama_pembayaran')
                    ->label('Metode Pembayaran')
                    ->searchable(),

                TextColumn::make('jenis_pembayaran')
                    ->badge()
                    ->searchable(),

                TextColumn::make('nilai')
                    ->money('IDR')
                    ->sortable(),

                ImageColumn::make('bukti_pembayaran_path')
                    ->label('Bukti Pembayaran')
                    ->disk('public')
                    ->square() // optional
                    // ->visibility('public') // opsional, jaga konsistensi
                    ->size(50),

                // ImageColumn::make('bukti_pembayaran_path')
                //     ->label('Bukti Pembayaran')
                //     ->disk('public')
                //     ->square()
                //     ->url(fn(Model $record): ?string => $record->bukti_pembayaran_path ? Storage::disk('public')->url($record->bukti_pembayaran_path) : null)
                //     ->openUrlInNewTab(),

                TextColumn::make('user.name')
                    ->label('Diinput oleh')
                    ->sortable(),
            ])
            ->filters([
                // TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make()->label('Buat Pembayaran Baru'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
