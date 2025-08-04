<?php

namespace App\Filament\Resources\VisiMatiResource\Pages;

use App\Filament\Resources\VisiMatiResource;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateVisiMati extends CreateRecord
{
    protected static string $resource = VisiMatiResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            Wizard::make([
                Wizard\Step::make('Detail Utama')
                    ->schema([
                        TextInput::make('nama')->required()->maxLength(255),
                        Textarea::make('deskripsi')->nullable(),
                    ]),
                Wizard\Step::make('Pilih Sub-Kategori')
                    ->schema([
                        CheckboxList::make('sub_kategori')
                            ->label('Pilih Sub-Kategori')
                            ->options([
                                'tabungan' => 'Tabungan',
                                'operasional' => 'Operasional',
                            ])
                            ->required()
                            ->minItems(1)
                            ->live(),
                    ]),
                Wizard\Step::make('Konfigurasi Sub-Kategori')
                    ->schema([
                        Section::make('Detail Tabungan')
                            ->schema([
                                TextInput::make('tabungan.nama')
                                    ->label('Nama Tabungan')
                                    ->required(fn (Get $get): bool => in_array('tabungan', $get('sub_kategori') ?? [])),
                                TextInput::make('tabungan.target_nominal')
                                    ->label('Target Nominal')
                                    ->numeric()
                                    ->required(fn (Get $get): bool => in_array('tabungan', $get('sub_kategori') ?? [])),
                                Select::make('tabungan.target_tipe')
                                    ->label('Target Tipe')
                                    ->options([
                                        'orang' => 'Orang',
                                        'bangunan' => 'Bangunan',
                                    ])
                                    ->required(fn (Get $get): bool => in_array('tabungan', $get('sub_kategori') ?? [])),
                            ])
                            ->visible(fn (Get $get): bool => in_array('tabungan', $get('sub_kategori') ?? [])),

                        Section::make('Detail Operasional')
                            ->schema([
                                TextInput::make('operasional.nama')
                                    ->label('Nama Operasional')
                                    ->required(fn (Get $get): bool => in_array('operasional', $get('sub_kategori') ?? [])),
                            ])
                            ->visible(fn (Get $get): bool => in_array('operasional', $get('sub_kategori') ?? [])),
                    ]),
            ])->columnSpanFull(),
        ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Create the main VisiMati record
        $visiMati = static::getModel()::create([
            'nama' => $data['nama'],
            'deskripsi' => $data['deskripsi'],
        ]);

        // Check if 'tabungan' was selected and create the related record
        if (in_array('tabungan', $data['sub_kategori'])) {
            $visiMati->tabungan()->create($data['tabungan']);
        }

        // Check if 'operasional' was selected and create the related record
        if (in_array('operasional', $data['sub_kategori'])) {
            $visiMati->operasional()->create($data['operasional']);
        }

        return $visiMati;
    }
}
