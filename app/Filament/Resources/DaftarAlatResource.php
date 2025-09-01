<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Pemilik;
use Filament\Forms\Form;
use App\Models\DaftarAlat;
use Filament\Tables\Table;
use App\Traits\GlobalForms;
use Filament\Pages\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use App\Filament\Resources\DaftarAlatResource\Pages;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use App\Filament\Resources\DaftarAlatResource\Pages\EditDaftarAlat;
use App\Filament\Resources\DaftarAlatResource\Pages\ListDaftarAlats;
use App\Filament\Resources\DaftarAlatResource\Pages\CreateDaftarAlat;


class DaftarAlatResource extends Resource
{
    use GlobalForms;
    protected static ?string $model = DaftarAlat::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench';
    protected static ?string $navigationLabel = 'Daftar Alat';
    protected static ?string $navigationGroup = 'Manajemen Data Master';
    protected static ?string $pluralModelLabel = 'Daftar Alat';
    protected static ?int $navigationSort = 3;
    protected static ?int $navigationGroupSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('jenis_alat_id')
                    ->relationship('jenisAlat', 'nama')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->validationMessages([
                        'required' => 'Jenis alat wajib dipilih.',
                    ])
                    ->createOptionForm([
                        TextInput::make('nama')
                            ->label('Nama Jenis Alat')
                            ->required(),
                        TextInput::make('keterangan')
                            ->label('Keterangan')
                            ->nullable(),
                    ]),
                TextInput::make('nomor_seri')
                    ->required()
                    ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule) {
                        $rule->where('company_id', Filament::getTenant()->id);
                        return $rule;
                    })
                    ->maxLength(255)
                    ->validationMessages([
                        'unique' => 'Nomor seri ini sudah terdaftar, silakan gunakan yang lain.',
                    ])
                    ->required(),
                Select::make('merk_id')
                    ->relationship('merk', 'nama')
                    ->searchable()
                    ->preload()
                    ->validationMessages([
                        'required' => 'Merk wajib dipilih.',
                    ])
                    ->createOptionForm([
                        TextInput::make('nama')
                            ->label('Nama Merk')
                            ->required(),
                    ])
                    ->required(),
                Select::make('pemilik_id')
                    ->relationship('pemilik', 'nama')
                    ->searchable()
                    ->preload()
                    ->options(function () {
                        return Pemilik::query()
                            ->where('company_id', \Filament\Facades\Filament::getTenant()?->getKey())
                            ->select('id', 'nama', 'nik')
                            ->get()
                            ->mapWithKeys(fn($pemilik) => [$pemilik->id => "{$pemilik->nama} - {$pemilik->nik}"]);
                    })
                    ->validationMessages([
                        'required' => 'Pemilik wajib dipilih.',
                    ])
                    ->createOptionForm([
                        Section::make('Informasi Pribadi')
                            ->schema([
                                TextInput::make('nama')
                                    ->label(label: 'Nama Pemilik (Sesuai KTP)')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('gender')
                                    ->dehydrated()
                                    ->label('Jenis Kelamin')
                                    ->options([
                                        'Pria' => 'Pria',
                                        'Wanita' => 'Wanita',
                                    ])
                                    ->required(),
                                TextInput::make('NIK')
                                    ->label('Nomor Induk Kependudukan (NIK)')
                                    ->string()
                                    ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule) {
                                        $rule->where('company_id', Filament::getTenant()->id);
                                        return $rule;
                                    })
                                    ->validationMessages([
                                        'unique' => 'NIK ini sudah terdaftar, silakan gunakan yang lain.',
                                    ])
                                    ->minLength(16)
                                    ->maxLength(16)
                                    ->required(),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule) {
                                        $rule->where('company_id', Filament::getTenant()->id);
                                        return $rule;
                                    })
                                    ->validationMessages([
                                        'unique' => 'Email ini sudah terdaftar, silakan gunakan yang lain.',
                                    ])
                                    ->email()
                                    ->required(),
                                TextInput::make('telepon')
                                    ->label('Nomor Telepon')
                                    ->tel()
                                    ->required(),
                            ])->columns(2),

                        Section::make('Alamat')
                            ->schema(self::getAddressFields())->columns(2),
                    ])
                    ->preload()
                    ->required(),
                Textarea::make('keterangan')
                    ->nullable()
                    ->columnSpanFull(),

                Select::make('kondisi')
                    ->label('Kondisi Alat')
                    ->required()
                    ->options([
                        true => 'Baik',
                        false => 'Bermasalah',
                    ])
                    ->visibleOn('edit'),

                Hidden::make('company_id')
                    ->default(fn() => \Filament\Facades\Filament::getTenant()?->getKey()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_seri')
                    ->searchable()
                    ->sortable()
                    ->label('Nomor Seri'),
                Tables\Columns\TextColumn::make('jenisAlat.nama')
                    ->searchable()
                    ->label('Jenis Alat')
                    ->sortable(),
                Tables\Columns\TextColumn::make('merk.nama')
                    ->searchable()
                    ->label('Merk Alat')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pemilik.nama')
                    ->searchable()
                    ->sortable()
                    ->label('Pemilik Alat'),

                BadgeColumn::make('kondisi')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Baik' : 'Bermasalah')
                    ->color(fn(bool $state): string => match ($state) {
                        true => 'success',
                        false => 'danger',
                    }),

                Tables\Columns\TextColumn::make('status_text')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Tersedia' => 'success',
                        'Dipakai' => 'warning',
                        'Terjual' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d-m-Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('jenis_alat')
                    ->relationship('jenisAlat', 'nama')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('kondisi')
                    ->label('Kondisi')
                    ->placeholder('Semua Kondisi')
                    ->trueLabel('Baik')
                    ->falseLabel('Bermasalah'),

                TernaryFilter::make('status')
                    ->label('Ketersediaan')
                    ->placeholder('Semua Status')
                    ->trueLabel('Tersedia')
                    ->falseLabel('Tidak Tersedia'),
                TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make(),
                // EditAction::make(),
                // DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
                ActivityLogTimelineTableAction::make('Log'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Alat Terdaftar')
            ->emptyStateDescription('Silahkan buat data alat baru untuk memulai.')
            ->defaultSort('created_at', 'desc');
    }

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withTrashed();
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
            'index' => Pages\ListDaftarAlats::route('/'),
            'create' => Pages\CreateDaftarAlat::route('/create'),
            'edit' => Pages\EditDaftarAlat::route('/{record}/edit'),
            'view' => Pages\ViewDaftarAlat::route('/{record}'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('kelola daftar alat'); // atau permission spesifik
    }
}
