<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Sales;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\TrefRegion;
use Filament\Tables\Table;
use App\Traits\GlobalForms;
use Filament\Pages\Actions;
use Filament\Facades\Filament;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use App\Filament\Resources\SalesResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SalesResource\RelationManagers;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class SalesResource extends Resource
{
    use GlobalForms;
    protected static ?string $model = Sales::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Manajemen Data Master';
    protected static ?int $navigationSort = 5;
    protected static ?string $pluralModelLabel = 'Sales';


    public static function form(Form $form): Form
    {
        $uuid = request()->segment(2);

        return $form
            ->schema([
                Section::make('Informasi Sales')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Sales')
                            ->required()
                            ->maxLength(50)
                            ->validationMessages([
                                'required' => 'Nama Sales harus diisi'
                            ]),
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
                        TextInput::make('email')
                            ->label('Email')
                            ->required()
                            ->email()
                            ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule) {
                                $rule->where('company_id', Filament::getTenant()->id);
                                return $rule;
                            })
                            ->maxLength(50)
                            ->validationMessages([
                                'required' => 'Email tidak boleh kosong',
                                'email' => 'Email tidak valid',
                                'regex' => 'Email tidak valid',
                                'unique' => 'Email sudah pernah terdaftar',
                            ]),
                        TextInput::make('telepon')
                            ->label('Telepon')
                            ->required()
                            ->tel()
                            ->validationMessages([
                                'required' => 'Telepon tidak boleh kosong',
                                'tel' => 'Nomor Telepon tidak valid',
                                'regex' => 'Nomor Telepon tidak valid'
                            ])
                            ->maxLength(15),
                    ])->columns(2),
                Section::make('Alamat')
                    ->schema(self::getAddressFields())->columns(2),
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
                TextColumn::make('nama'),
                TextColumn::make('email'),
                TextColumn::make('telepon'),
                TextColumn::make('user.name')
                    ->label('Dibuat Oleh')
                    ->sortable()
                    ->searchable(),

            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                ActivityLogTimelineTableAction::make('Log'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),

                ]),
            ])
            ->emptyStateHeading('Belum Ada Sales Terdaftar')
            ->emptyStateDescription('Silahkan buat data sales baru untuk memulai.')
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSales::route('/create'),
            'edit' => Pages\EditSales::route('/{record}/edit'),
            'view' => Pages\ViewSales::route('/{record}'),
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

    public static function canAccess(): bool
    {
        return auth()->user()->can('kelola sales'); // atau permission spesifik
    }
}
