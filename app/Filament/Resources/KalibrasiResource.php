<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Kalibrasi;
use Filament\Pages\Actions;
use App\Models\Perorangan;
use Filament\Tables\Table;
use App\Traits\GlobalForms;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use App\Filament\Resources\KalibrasiResource\Pages;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;
use App\Filament\Resources\KalibrasiResource\RelationManagers\PengajuanDanasRelationManager;
use App\Filament\Resources\KalibrasiResource\RelationManagers\DetailKalibrasiRelationManager;
use App\Filament\Resources\KalibrasiResource\RelationManagers\StatusPembayaranRelationManager;

class KalibrasiResource extends Resource
{
    use GlobalForms;
    protected static ?string $model = Kalibrasi::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Kalibrasi';
    protected static ?string $navigationGroup = 'Layanan';
    protected static ?string $pluralModelLabel = 'Jasa Kalibrasi';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Customer')
                    ->schema(self::getCustomerForm()),
                Section::make('Informasi Kalibrasi')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Kalibrasi')
                            ->required(),
                        TextInput::make('harga')
                            ->label('Harga')
                            ->numeric()
                            ->prefix('Rp ')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(','),
                        Select::make('status')
                            ->options([
                                'dalam_proses' => 'Dalam proses',
                                'selesai' => 'Selesai'
                            ])
                            ->visibleOn('edit')
                            ->default('dalam_proses')
                            ->native(false),
                    ]),
                Hidden::make('company_id')
                    ->default(fn() => \Filament\Facades\Filament::getTenant()?->getKey()),
            ]);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        // Simpan relasi many-to-many dengan peran
        if (isset($data['customer_flow_type']) && $data['customer_flow_type'] === 'perorangan' && isset($data['perorangan_id'])) {
            $data['perorangan_ids'] = [$data['perorangan_id']];
            unset($data['perorangan_id']);
        }

        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        // Simpan relasi many-to-many dengan peran
        if (isset($data['customer_flow_type']) && $data['customer_flow_type'] === 'perorangan' && isset($data['perorangan_id'])) {
            $data['perorangan_ids'] = [$data['perorangan_id']];
            unset($data['perorangan_id']);
        }

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')->sortable()->searchable()->wrap(),
                Tables\Columns\TextColumn::make('customer_display')
                    ->label('Klien Utama')
                    ->state(function (Kalibrasi $record): string {
                        if ($record->corporate) {
                            return $record->corporate->nama;
                        }
                        return $record->perorangan->first()?->nama ?? 'N/A';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->whereHas('corporate', fn($q) => $q->where('nama', 'like', "%{$search}%"))
                            ->orWhereHas('perorangan', fn($q) => $q->where('nama', 'like', "%{$search}%"));
                    }),
                Tables\Columns\TextColumn::make('perorangan.nama')
                    ->label('PIC')
                    ->listWithLineBreaks()
                    ->limitList(2),
                Tables\Columns\TextColumn::make('nama'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'dalam_proses' => 'primary',
                        'selesai' => 'success',
                        default => 'primary'
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                ActivityLogTimelineTableAction::make('Log'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');;
    }

    public static function getRelations(): array
    {
        return [
            PengajuanDanasRelationManager::class,
            DetailKalibrasiRelationManager::class,
            StatusPembayaranRelationManager::class,
            ActivitylogRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKalibrasis::route('/'),
            'create' => Pages\CreateKalibrasi::route('/create'),
            'edit' => Pages\EditKalibrasi::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withTrashed();
    }

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
