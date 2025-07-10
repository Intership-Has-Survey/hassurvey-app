<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\StatusPekerjaan;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StatusPekerjaanResource\Pages;

class StatusPekerjaanResource extends Resource
{
    protected static ?string $model = StatusPekerjaan::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
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
                Select::make('pekerjaan_lapangan')
                    ->options([
                        'selesai' => 'selesai',
                        'tidak selesai' => 'tidak selesai',
                    ])
                    ->required()
                    ->native(false), // Menggunakan styling modern Filament

                // Pilihan status berdasarkan ENUM di migrasi
                Select::make('proses_data_dan_gambar')
                    ->options([
                        'Tidak Selesai' => 'Tidak Selesai',
                        'Selesai' => 'Selesai',
                        'Tidak Perlu' => 'Tidak Perlu',
                    ])
                    ->required()
                    ->native(false),

                Select::make('laporan')
                    ->options([
                        'Tidak Selesai' => 'Tidak Selesai',
                        'Selesai' => 'Selesai',
                        'Tidak Perlu' => 'Tidak Perlu',
                    ])
                    ->required()
                    ->native(false),

                Textarea::make('keterangan')
                    ->columnSpanFull(),
                Hidden::make('user_id')
                    ->default(auth()->user()->id),
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

                TextColumn::make('pekerjaan_lapangan')
                    ->searchable(),

                // Menampilkan status dengan badge berwarna agar mudah dilihat
                TextColumn::make('proses_data_dan_gambar')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Selesai' => 'success',
                        'Tidak Selesai' => 'warning',
                        'Tidak Perlu' => 'gray',
                    }),

                TextColumn::make('user.name')
                    ->label('Editor')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('updated_at')
                    ->label('Tanggal Update')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('project_id')
                    ->label('Proyek')
                    ->relationship('project', 'nama_project'),
            ])->filters([
                SelectFilter::make('project_id')
                    ->label('Proyek')
                    ->relationship('project', 'nama_project'),

                SelectFilter::make('proses_data_dan_gambar')
                    ->label('Proses Data & Gambar')
                    ->options([
                        'Selesai' => 'Selesai',
                        'Tidak Selesai' => 'Tidak Selesai',
                        'Tidak Perlu' => 'Tidak Perlu',
                    ]),

                SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id(); // aman, otomatis ambil user yang sedang login
        return $data;
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
