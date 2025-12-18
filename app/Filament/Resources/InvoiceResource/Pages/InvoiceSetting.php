<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use Filament\Forms;
use App\Models\Invoice;
use Filament\Forms\Form;
use Filament\Facades\Filament;
use App\Models\InvoicesSetting;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Route;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\RichEditor;
use App\Filament\Resources\InvoiceResource;


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
                RichEditor::make('catatan')
                    ->label('Catatan')
                    ->disableToolbarButtons([
                        'attachFiles',
                        'blockquote',
                        'codeBlock',
                        'h2',
                        'h3',
                        'link',
                        'strike',
                        'underline',
                    ]),
                RichEditor::make('penutup')
                    ->label('Data Pembayaran')
                    ->disableToolbarButtons([
                        'attachFiles',
                        'blockquote',
                        'codeBlock',
                        'h2',
                        'h3',
                        'link',
                        'strike',
                        'underline',
                    ]),
                TextInput::make('signature_name')
                    ->label('Signature')
                    ->helperText('Teks di atas nama penandatangan contoh: Hormat Kami, Dengan Hormat, dll')
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

    public function getPreviewUrl(): string
    {
        $companyId = Filament::getTenant()->getKey();

        // Coba ambil invoice contoh untuk preview
        $sampleInvoice = InvoicesSetting::where('company_id', $companyId)
            ->latest()
            ->first();

        if ($sampleInvoice) {
            // Jika ada invoice, gunakan invoice terbaru
            return route('invoicepreview', [
                'company' => $companyId
            ]);
        }

        // Jika tidak ada invoice, buat URL untuk preview template
        return route('invoicepreview', [
            'company' => $companyId
        ]);
    }
}
