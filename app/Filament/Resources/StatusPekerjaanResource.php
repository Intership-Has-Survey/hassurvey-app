<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\StatusPekerjaan;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StatusPekerjaanResource\Pages;

class StatusPekerjaanResource extends Resource
{
    protected static ?string $model = StatusPekerjaan::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Status Pekerjaan';
    protected static ?string $navigationGroup = 'Jasa Pemetaan';
    protected static ?int $navigationSort = 3;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Field untuk memilih proyek terkait
                Select::make('project_id')
                    ->relationship('project', 'nama_project')
                    ->searchable()
                    ->preload() // Memuat opsi saat halaman dibuka untuk UX yang lebih baik
                    ->required()
                    ->label('Nama Proyek'),

                // Pilihan jenis pekerjaan berdasarkan ENUM di migrasi
                Select::make('pekerjaan')
                    ->options([
                        'Pekerjaan Lapangan' => 'Pekerjaan Lapangan',
                        'Input Data' => 'Input Data',
                        'Laporan' => 'Laporan',
                    ])
                    ->required()
                    ->native(false), // Menggunakan styling modern Filament

                // Pilihan status berdasarkan ENUM di migrasi
                Select::make('status')
                    ->options([
                        'Belum Selesai' => 'Belum Selesai',
                        'Tidak Selesai' => 'Tidak Selesai',
                        'Selesai' => 'Selesai',
                        'Tidak Perlu' => 'Tidak Perlu',
                    ])
                    ->required()
                    ->native(false),

                Textarea::make('keterangan')
                    ->columnSpanFull(),

                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('User')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Menampilkan nama proyek dari relasi
                TextColumn::make('project.nama_project')
                    ->label('Nama Proyek')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('pekerjaan')
                    ->searchable(),

                // Menampilkan status dengan badge berwarna agar mudah dilihat
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Selesai' => 'success',
                        'Belum Selesai' => 'warning',
                        'Tidak Selesai' => 'danger',
                        'Tidak Perlu' => 'gray',
                    }),

                // Menampilkan siapa yang terakhir mengupdate
                TextColumn::make('user.name')
                    ->label('Diupdate oleh')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Tanggal Update')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStatusPekerjaans::route('/'),
            'create' => Pages\CreateStatusPekerjaan::route('/create'),
            'edit' => Pages\EditStatusPekerjaan::route('/{record}/edit'),
        ];
    }
}
