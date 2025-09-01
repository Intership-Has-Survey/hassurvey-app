<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Pages\Actions;
use App\Models\JenisPekerjaan;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
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
use App\Filament\Resources\JenisPekerjaanResource\Pages;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use App\Filament\Resources\JenisPekerjaanResource\Pages\EditJenisPekerjaan;
use App\Filament\Resources\JenisPekerjaanResource\Pages\ListJenisPekerjaans;
use App\Filament\Resources\JenisPekerjaanResource\Pages\CreateJenisPekerjaan;

class JenisPekerjaanResource extends Resource
{
    protected static ?string $model = JenisPekerjaan::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-bug-ant';
    protected static ?string $navigationLabel = 'Jenis Pekerjaan';
    protected static ?string $navigationGroup = 'Jasa Pemetaan';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                TextInput::make('nama')->required()->label('Jenis Pekerjaan'),
                TextInput::make('keterangan')
                    ->label('Keterangan')
                    ->maxLength(300),
                Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama'),
                Tables\Columns\TextColumn::make('keterangan'),
                Tables\Columns\TextColumn::make('user.name')->label('Dibuat oleh')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
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
            'index' => Pages\ListJenisPekerjaans::route('/'),
            'create' => Pages\CreateJenisPekerjaan::route('/create'),
            'edit' => Pages\EditJenisPekerjaan::route('/{record}/edit'),
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
