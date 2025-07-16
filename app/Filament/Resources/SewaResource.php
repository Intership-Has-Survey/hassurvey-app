<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SewaResource\Pages;
use App\Filament\Resources\SewaResource\RelationManagers;
use App\Models\Sewa;
use App\Models\Perorangan;
use App\Models\Corporate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Components\FileUpload;

class SewaResource extends Resource
{
    protected static ?string $model = Sewa::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Penyewaan Alat';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Kontrak')
                    ->schema([
                        Forms\Components\TextInput::make('judul')
                            ->required()
                            ->label('Judul Penyewaan'),
                        Forms\Components\DatePicker::make('tgl_mulai')
                            ->required(),
                        Forms\Components\DatePicker::make('tgl_selesai')
                            ->required()
                            ->minDate(fn(Get $get) => $get('tgl_mulai')),
                        Forms\Components\TextInput::make('lokasi')
                            ->required(),
                        Forms\Components\Textarea::make('alamat')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Informasi Customer')
                    ->schema([
                        Select::make('customer_type')
                            ->label('Tipe Customer')
                            ->options([
                                Perorangan::class => 'Perorangan',
                                Corporate::class => 'Corporate',
                            ])
                            ->live()
                            ->required()
                            ->placeholder('Pilih tipe customer terlebih dahulu'),

                        Select::make('customer_id')
                            ->label('Pilih Customer')
                            ->options(function (Get $get): array {
                                $type = $get('customer_type');
                                if (!$type)
                                    return [];
                                return $type::pluck('nama', 'id')->all();
                            })
                            ->searchable()
                            ->required()
                            ->createOptionForm(function (Get $get) {
                                $type = $get('customer_type');
                                if ($type === Perorangan::class) {
                                    return [
                                        TextInput::make('nama')->required()->label('Nama Lengkap (Sesuai KTP)'),
                                        Select::make('gender')->options(['Pria', 'Wanita'])->required()->label('Jenis Kelamin'),
                                        TextInput::make('email')->email()->required()->unique(Perorangan::class, 'Email'),
                                        TextInput::make('telepon')->tel()->required(),
                                        TextInput::make('alamat')->required()->columnSpanFull(),
                                        TextInput::make('nik')->label('NIK')->numeric()->length(16)->required()->unique(Perorangan::class, 'nik', ignoreRecord: true),
                                        FileUpload::make('foto_ktp')->label('Foto KTP')->image()->required(),
                                        FileUpload::make('foto_kk')->label('Foto KK')->image()->required(),
                                    ];
                                }
                                if ($type === Corporate::class) {
                                    return [
                                        TextInput::make('nama')->label('Nama Perusahaan')->required(),
                                        Select::make('level')->options(['Kecil', 'Menengah', 'Besar'])->required(),
                                        TextInput::make('email')->email()->required()->unique(Corporate::class, 'email'),
                                        TextInput::make('telepon')->tel()->required(),
                                        TextInput::make('alamat')->required(),
                                        TextInput::make('nib')->nullable()->label('NIB (Nomor Induk Berusaha)')
                                    ];
                                }
                                return [];
                            })
                            ->createOptionUsing(function (array $data, Get $get): ?string {
                                $type = $get('customer_type');
                                if (!$type)
                                    return null;

                                $data['user_id'] = auth()->id();

                                $record = $type::create($data);
                                return $record->id;
                            })
                            ->visible(fn(Get $get) => filled($get('customer_type'))),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul Penyewaan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.nama')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_mulai')
                    ->date('d-m-Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_selesai')
                    ->date('d-m-Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('lokasi')
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            RelationManagers\RiwayatSewasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSewa::route('/'),
            'create' => Pages\CreateSewa::route('/create'),
            'edit' => Pages\EditSewa::route('/{record}/edit'),
        ];
    }
}
