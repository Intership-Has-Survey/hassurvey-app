<?php

namespace App\Filament\Resources\CorporateResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Perorangan;
use App\Models\TrefRegion;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\AttachAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class PeroranganRelationManager extends RelationManager
{
    protected static string $relationship = 'perorangan';
    protected static ?string $title = 'PIC';
    protected static bool $isLazy = false;
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                TextInput::make('nik')
                    ->nullable()
                    ->maxLength(16)
                    ->unique(ignoreRecord: true)
                    ->label('NIK'),
                Select::make('gender')
                    ->options([
                        'Pria' => 'Pria',
                        'Wanita' => 'Wanita',
                    ])
                    ->required()
                    ->label('Jenis Kelamin'),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('telepon')
                    ->tel()
                    ->required()
                    ->label('Nomor Telepon'),
                Section::make('Alamat PIC')
                    ->schema(self::getAddressFields())->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('PIC (Person in Charge)')
            ->recordTitleAttribute('nama')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telepon'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Tambahkan/Pilih PIC')

                    ->form(fn(AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Pilih PIC')
                            ->createOptionForm(fn(Form $form) => $this->form($form))
                            ->createOptionAction(fn(Forms\Components\Actions\Action $action) => $action->label('Buat PIC Baru'))
                            ->createOptionUsing(function (array $data): string {
                                $data['user_id'] = auth()->id();
                                return Perorangan::create($data)->getKey();
                            }),
                        Hidden::make('user_id')
                            ->default(auth()->id()),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
