<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use App\Models\StatusPembayaran;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class StatusPembayaranRelationManager extends RelationManager
{

    protected static string $relationship = 'StatusPembayaran';

    protected static ?string $title = 'Riwayat Pembayaran';

    protected static bool $isLazy = false;

    public function form(Form $form): Form
    {
        $project = $this->ownerRecord;
        $nilaiProyek = (float) $project->nilai_project;
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
                    ->native(false),

                Select::make('jenis_pembayaran')
                    ->options([
                        'DP' => 'DP',
                        'Pelunasan' => 'Pelunasan',
                        'Termin 1' => 'Termin 1',
                        'Termin 2' => 'Termin 2',
                    ])
                    ->required()
                    ->native(false),

                TextInput::make('nilai')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->prefix('Rp')
                    ->maxlength(20),

                FileUpload::make('bukti_pembayaran_path')
                    ->label('Bukti Pembayaran')
                    ->acceptedFileTypes(['image/*', 'application/pdf'])
                    ->maxSize(1024) // 1 MB
                    ->required()
                    ->image()
                    ->directory('bukti-pembayaran'),

                Hidden::make('user_id')
                    ->default(auth()->id()),
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

                TextColumn::make('user.name')
                    ->label('Diinput oleh')
                    ->sortable(),

                ImageColumn::make('bukti_pembayaran_path')
                    ->disk('public')
                    ->label('Bukti Pembayaran')
                    ->circular()
                // ->visible(fn($record) => $record->bukti_pembayaran_path),

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
