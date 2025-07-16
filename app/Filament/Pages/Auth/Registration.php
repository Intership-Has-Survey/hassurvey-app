<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class Registration extends BaseRegister
{
    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Name')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->maxLength(255),

            Select::make('role')
                ->label('role')
                ->options([
                    'admin' => 'Admin',
                    'operasional' => 'operasional',
                    'dirops' => 'Dirops',
                    'keuangan' => 'Keuangan',
                    'direktur' => 'Direktur',
                ])
                ->label('keuangan')
                ->label('direktur')
                ->required()
                ->maxLength(255),

            TextInput::make('password')
                ->label('Password')
                ->password()
                ->required()
                ->maxLength(255),

            TextInput::make('passwordConfirmation')
                ->label('Confirm Password')
                ->password()
                ->required()
                ->same('password'),
        ]);
    }

    public function getNameFormComponent(): TextInput
    {
        return TextInput::make('name')
            ->label('sayahrial kelas kakap 5 sd pabuaran')
            ->required()
            ->maxLength(100);
    }
}
