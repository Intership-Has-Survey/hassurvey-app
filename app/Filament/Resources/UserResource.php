<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
// use App\Filament\Resources\Auth;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Pages\Actions;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $tenantOwnershipRelationshipName = 'companies';

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Kelola Akun';

    protected static ?string $pluralModelLabel = 'Akun Pengguna';

    protected static ?string $navigationGroup = 'Manajemen Data Master';

    protected static ?int $navigationSort = 1;

    protected static ?int $navigationGroupSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Super Admin');
            })
            ->withTrashed();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama')
                    ->placeholder('Masukkan nama User')
                    ->required(),
                TextInput::make('email')
                    ->label('Email')
                    ->unique(ignoreRecord: true)
                    ->placeholder('Masukkan Email yang bener bos!')
                    ->email()
                    ->validationMessages([
                        'unique' => 'Email sudah pernah terdaftar',
                    ])
                    ->required(),
                TextInput::make('password')
                    ->label('Password')
                    ->placeholder(fn(string $context) => $context === 'create' ? 'Gunakan password yang kuat' : 'Kosongkan jika tidak ingin mengubah password')
                    ->dehydrated(fn($state) => filled($state))
                    ->password()
                    ->revealable()
                    ->required(fn(string $context) => $context === 'create')
                    ->mutateDehydratedStateUsing(fn($state) => filled($state) ? Hash::make($state) : null),
                Select::make('roles')
                    // ->multiple()
                    ->relationship('roles', 'name', fn($query) => $query->where('company_id', \Filament\Facades\Filament::getTenant()?->getKey()))
                    ->preload()
                    ->required()
                    ->validationMessages([
                        'required' => 'Jabatan wajib diisi',
                    ])
                    ->label('Beri Jabatan'),
                Select::make('companies')
                    ->label('Akses Perusahaan')
                    ->multiple()
                    ->required()
                    ->relationship('companies', 'name')
                    ->preload()
                    ->options(function () {
                        return Auth::user()
                            ->companies() // relasi many-to-many
                            ->pluck('name', 'id'); // key = id, value = name
                    })
                    ->validationMessages([
                        'required' => 'Pilih setidaknya satu perusahaan/company',
                    ]),
                // Select::make('roles')->multiple()->relationship('roles', 'name')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('nama')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
                TextColumn::make('roles.name')->sortable()->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make(),
                // EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                ActivityLogTimelineTableAction::make('Log'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Pengguna Terdaftar')
            ->emptyStateDescription('Silahkan buat pengguna baru untuk memulai.')
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
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
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('kelola akun pengguna'); // atau permission spesifik
    }
}
