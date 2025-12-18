<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\Page;
use App\Models\InvoicesSetting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextArea;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;


class InvoiceSetting extends Page
{
    protected static string $resource = InvoiceResource::class;

    protected static string $view = 'filament.resources.invoice-resource.pages.invoice-setting';

    use Forms\Concerns\InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $setting = InvoicesSetting::where(
            'company_id',
            Filament::getTenant()->getKey()
        )->first();

        $this->form->fill($setting?->toArray() ?? []);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama_perusahaan')
                    ->label('Nama Perusahaan'),
                Textarea::make('alamat')
                    ->label('Alamat Lengkap')
                    ->rows(4),
                TextInput::make('telepon')
                    ->label('Nomor Telepon'),
                TextInput::make('mobile')
                    ->label('Nomor Mobile'),
                TextInput::make('email')
                    ->label('Email Perusahaan'),
                Textarea::make('catatan')
                    ->label('Catatan')
                    ->rows(4),
                Textarea::make('penutup')
                    ->label('Penutup')
                    ->rows(4),
                TextInput::make('signature_name')
                    ->label('Signature')
                    ->default('Nama Penandatangan'),
                TextInput::make('nama')
                    ->label('Nama Penandatangan'),
                TextInput::make('jabatan')
                    ->label('Jabatan Penandatangan'),
                TextInput::make('company_id')
                    // ->readonly()
                    ->helperText('Akan otomatis terisi dengan Company ID')
                    ->default('haaha'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            // 🔐 Inject tenant DI SINI (SATU-SATUNYA TEMPAT)
            $data['company_id'] = Filament::getTenant()->getKey();

            InvoicesSetting::updateOrCreate(
                ['company_id' => $data['company_id']],
                $data
            );

            Notification::make()
                ->title('Setting berhasil disimpan')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Setting gagal')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
