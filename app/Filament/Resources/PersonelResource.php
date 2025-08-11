<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Personel;
use Filament\Forms\Form;
use App\Models\TrefRegion;
use Filament\Tables\Table;
use App\Traits\GlobalForms;
use Filament\Pages\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreBulkAction;
use App\Filament\Resources\PersonelResource\Pages;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PersonelResource\RelationManagers;
use App\Filament\Resources\PersonelResource\Pages\EditPersonel;
use App\Filament\Resources\PersonelResource\Pages\ListPersonels;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use App\Filament\Resources\PersonelResource\Pages\CreatePersonel;
use App\Filament\Resources\PersonelResource\RelationManagers\ProjectPersonelRelationManager;
use App\Filament\Resources\PersonelResource\RelationManagers\PembayaranPersonelRelationManager;

class PersonelResource extends Resource
{
    use GlobalForms;
    protected static ?string $model = Personel::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Personel';
    protected static ?string $navigationGroup = 'Manajemen Data Master';

    protected static ?string $pluralModelLabel = 'Personel';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Pribadi')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Lengkap')
                            ->required(),
                        TextInput::make('nik')
                            ->label('Nomor Induk Kependudukan (NIK)')
                            ->required()
                            ->length(16)
                            ->rule('regex:/^\d+$/')
                            ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule) {
                                $rule->where('company_id', Filament::getTenant()->id);
                                return $rule;
                            })
                            ->validationMessages([
                                'required' => 'NIK tidak boleh kosong',
                                'unique' => 'NIK sudah pernah terdaftar',
                                'regex' => 'NIK hanya boleh berisi angka',
                            ]),
                        TextInput::make('nomor_wa')
                            ->label('Nomor WhatsApp')
                            ->required()
                            ->tel()
                            ->validationMessages([
                                'required' => 'Nomor WhatsApp tidak boleh kosong',
                                'tel' => 'Nomor WhatsApp harus berformat nomor telepon',
                                'regex' => 'Nomor WhatsApp harus berformat nomor telepon',
                            ]),
                    ])->columns(2),

                Section::make('Informasi Pekerjaan')
                    ->schema([
                        Select::make('tipe_personel')
                            ->label('Tipe Personel')
                            ->options([
                                'internal' => 'Internal',
                                'freelance' => 'Freelance',
                            ])
                            ->required()
                            ->validationMessages([
                                'required' => 'Tipe Personel tidak boleh kosong',
                            ])
                            ->native(false),

                        Select::make('jabatan')
                            ->options([
                                'surveyor' => 'Surveyor',
                                'asisten surveyor' => 'Asisten Surveyor',
                                'driver' => 'Driver',
                                'drafter' => 'Drafter',
                            ])
                            ->required()
                            ->validationMessages([
                                'required' => 'Jabatan tidak boleh kosong',
                            ])
                            ->native(false),

                        // Textarea::make('keterangan')
                        //     ->label('Keterangan')
                        //     ->nullable()
                        //     ->placeholder('Kosongkan jika tidak ada keterangan khusus')
                        //     ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Alamat')
                    ->schema(self::getAddressFields())->columns(2),

                Hidden::make('user_id')
                    ->default(auth()->id()),
                Hidden::make('company_id')
                    ->default(fn() => \Filament\Facades\Filament::getTenant()?->getKey()),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tipe_personel')
                    ->label('Tipe Personel')
                    ->badge()
                    ->color(
                        fn(string $state): string =>
                        str_contains($state, 'internal') ? 'primary' : 'info'
                    )
                    ->sortable()
                    ->searchable(),
                TextColumn::make('jabatan')
                    ->label('Jabatan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nama')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(
                        fn(string $state): string => $state === 'Tersedia' ? 'success' : 'warning'
                    ),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user.name')
                    ->label('Dibuat Oleh')
                    ->sortable()
                    ->searchable(),
            ])

            ->filters([

                SelectFilter::make('jabatan')
                    ->label('Jabatan')
                    ->searchable()
                    ->options(function () {
                        return Personel::query()
                            ->select('jabatan')
                            ->distinct()
                            ->pluck('jabatan', 'jabatan');
                    }),

                SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
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
            ->emptyStateHeading('Belum Ada Personel Terdaftar')
            ->emptyStateDescription('Silahkan buat personel baru untuk memulai.')
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
            RelationManagers\ProjectPersonelRelationManager::class,
            RelationManagers\PembayaranPersonelRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonels::route('/'),
            'create' => Pages\CreatePersonel::route('/create'),
            'edit' => Pages\EditPersonel::route('/{record}/edit'),
            'view' => Pages\ViewPersonel::route('/{record}'),
        ];
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
}
