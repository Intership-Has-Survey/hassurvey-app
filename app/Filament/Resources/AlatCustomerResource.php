<?php

namespace App\Filament\Resources;

use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Corporate;
use App\Traits\GlolForms;
use App\Models\Perorangan;
use Filament\Tables\Table;
use App\Traits\GlobalForms;
use Filament\Pages\Actions;
use App\Models\AlatCustomer;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use App\Filament\Resources\AlatCustomerResource\Pages\EditAlatCustomer;
use App\Filament\Resources\AlatCustomerResource\Pages\ListAlatCustomers;
use App\Filament\Resources\AlatCustomerResource\Pages\CreateAlatCustomer;
use App\Filament\Resources\AlatCustomerResource\Pages\ViewAlatCustomer;
use App\Filament\Resources\AlatCustomerResource\RelationManagers\DetailKalibrasiRelationManager;

class AlatCustomerResource extends Resource
{
    use GlobalForms;
    protected static ?string $model = AlatCustomer::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Alat Customer';
    protected static ?string $navigationGroup = 'Customer';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Alat')
                    ->schema([
                        Select::make('jenis_alat_id')
                            ->label('Jenis Alat')
                            ->relationship('jenisAlat', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->validationMessages([
                                'required' => 'Jenis Alat wajib dipilih.',
                            ])
                            ->createOptionForm([
                                TextInput::make('nama')
                                    ->label('Nama Jenis Alat')
                                    ->unique(ignoreRecord: true)
                                    ->required()
                                    ->validationMessages([
                                        'unique' => 'Nama alat ini sudah terdaftar, silakan gunakan yang lain.',
                                    ]),
                                TextInput::make('keterangan')
                                    ->label('Keterangan')
                                    ->nullable(),
                            ]),
                        TextInput::make('nomor_seri')
                            ->required()
                            ->label('Nomor Seri')
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
                            ->createOptionForm([
                                TextInput::make('nama')
                                    ->label('Nama Merk')
                                    ->required(),
                            ])
                            ->required()
                            ->validationMessages([
                                'required' => 'Merk wajib dipilih.',
                            ]),

                        Select::make('kondisi')
                            ->label('Kondisi Alat')
                            ->required()
                            ->options([
                                true => 'Baik',
                                false => 'Dipakai',
                            ])
                            ->visibleOn('edit'),
                        Textarea::make('keterangan')
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
                Section::make('Informasi Customer')
                    ->schema(self::getCustomerForm()),

                Hidden::make('company_id')
                    ->default(fn() => \Filament\Facades\Filament::getTenant()?->getKey()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('corporate_id')
                    ->label('Customer')
                    ->getStateUsing(function ($record) {
                        return optional($record->corporate)->nama
                            ?? optional($record->perorangan)->nama
                            ?? 'Tidak ada customer';
                    }),
                TextColumn::make('jenisalat.nama')->label('Jenis Alat'),
                TextColumn::make('merk.nama')->label('Merek'),
                TextColumn::make('nomor_seri'),
            ])
            ->filters([
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
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            DetailKalibrasiRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAlatCustomers::route('/'),
            'create' => CreateAlatCustomer::route('/create'),
            'edit' => EditAlatCustomer::route('/{record}/edit'),
            'view' => ViewAlatCustomer::route('/{record}'),
            // 'view' => ViewAlatC::route('/{record}'),
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
