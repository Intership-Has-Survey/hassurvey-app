<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\StatusPembayaran;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Resources\StatusPembayaranResource\Pages;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use App\Filament\Resources\StatusPembayaranResource\RelationManagers;
use App\Filament\Resources\StatusPembayaranResource\Pages\ListStatusPembayarans;
use App\Filament\Resources\StatusPembayaranResource\Pages\CreateStatusPembayaran;


class StatusPembayaranResource extends Resource
{
    protected static ?string $model = StatusPembayaran::class;
    // protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Pemasukan';
    protected static ?string $title = 'Pemasukan';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $pluralModelLabel = 'Pemasukan';

    public static function form(Form $form): Form
    {

        $uuid = request()->segment(2);
        return $form
            ->schema([
                // Field untuk memilih proyek terkait
                // Select::make('project_id')
                //     ->relationship('project', 'nama_project')
                //     ->searchable()
                //     ->preload()
                //     ->label('Proyek')
                //     ->required(),

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
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),

                Hidden::make('user_id')
                    ->default(auth()->id()),

                Hidden::make('company_id')
                    ->default($uuid),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('payable_type')
                    ->label('Jenis Layanan')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'App\Models\Project' => 'Jasa Pemetaan',
                        'App\Models\Sewa' => 'Sewa',
                        'App\Models\Kalibrasi' => 'Kalibrasi',
                        'App\Models\Penjualan' => 'Penjualan',
                        default => 'Lainnya'
                    }),
                TextColumn::make('nama_layanan')
                    ->label('Judul Layanan')
                    ->getStateUsing(function ($record) {
                        return match ($record->payable_type) {
                            'App\\Models\\Project' => $record->payable?->nama_project,
                            'App\\Models\\Sewa' => $record->payable?->judul,
                            'App\\Models\\Kalibrasi' => $record->payable?->nama,
                            'App\\Models\\Penjualan' => $record->payable?->nama_penjualan,
                            default => '-'
                        };
                    }),
                TextColumn::make('nama_pembayaran')
                    ->label('Metode Pembayaran')
                    ->searchable(),

                TextColumn::make('jenis_pembayaran')
                    ->badge()
                    ->searchable(),

                TextColumn::make('nilai')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('user.name')->label('Dibuat Oleh'),
            ])
            ->filters([
                // TrashedFilter::make(),
                Filter::make('Periode')
                    ->form([
                        DatePicker::make('start_date')->label('Dari Tanggal'),
                        DatePicker::make('end_date')->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        session([
                            'filter_start_date' => $data['start_date'] ?? null,
                            'filter_end_date' => $data['end_date'] ?? null,
                        ]);

                        return $query
                            ->when($data['start_date'], fn($query) => $query->whereDate('created_at', '>=', $data['start_date']))
                            ->when($data['end_date'], fn($query) => $query->whereDate('created_at', '<=', $data['end_date']));
                    }),

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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStatusPembayarans::route('/'),
            'create' => Pages\CreateStatusPembayaran::route('/create'),
            'view' => Pages\ViewStatusPembayaran::route('/{record}'),
            'edit' => Pages\EditStatusPembayaran::route('/{record}/edit'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatusPembayaranSummary::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatusPembayaranSummary::class,
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    // public static function getWidgets(): array
    // {
    //     return [
    //         \App\Filament\Resources\StatusPembayaranResource\Widgets\TotalPembayaran::class,
    //     ];
    // }
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
