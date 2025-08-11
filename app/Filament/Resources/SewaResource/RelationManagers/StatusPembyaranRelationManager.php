<?php

namespace App\Filament\Resources\SewaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;

class StatusPembyaranRelationManager extends RelationManager
{
    protected static string $relationship = 'StatusPembayaran';

    public function form(Form $form): Form
    {
        $project = $this->ownerRecord;
        $nilaiProyek = (float) $project->harga_fix;
        $totalDibayar = (float) $project->statusPembayaran()->sum('nilai');
        $sisaPembayaran = $nilaiProyek - $totalDibayar;

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
                    ->native(false)
                    ->validationMessages([
                        'required' => 'Pilih salah satu metode pembayaran',
                    ]),

                Select::make('jenis_pembayaran')
                    ->options([
                        'DP' => 'DP',
                        'Pelunasan' => 'Pelunasan',
                        'Termin 1' => 'Termin 1',
                        'Termin 2' => 'Termin 2',
                    ])
                    ->required()
                    ->native(false)
                    ->validationMessages([
                        'required' => 'Pilih salah satu jenis pembayaran',
                    ]),

                TextInput::make('nilai')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->prefix('Rp')
                    ->maxlength(20)
                    ->required()
                    ->validationMessages([
                        'required' => 'Masukkan nilai pembayaran',
                    ]),

                FileUpload::make('bukti_pembayaran_path')
                    ->label('Bukti Pembayaran')
                    ->image()
                    ->maxSize(1024)
                    ->required()
                    ->validationMessages([
                        'required' => 'Masukkan bukti pembayaran',
                        'max_size' => 'Ukuran file maksimal 1 MB',
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
                    ->square()
                    ->url(fn(Model $record): ?string => $record->bukti_pembayaran_path ? Storage::disk('public')->url($record->bukti_pembayaran_path) : null)
                    ->openUrlInNewTab(),

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
