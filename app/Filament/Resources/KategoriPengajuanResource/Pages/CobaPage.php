<?php

namespace App\Filament\Resources\KategoriPengajuanResource\Pages;

use App\Filament\Resources\KategoriPengajuanResource;
use App\Models\Acara;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use App\Models\Invoice;
use Filament\Forms\Form;
use Filament\Facades\Filament;
use App\Models\AcaraSetting;
use Illuminate\Support\Facades\Route;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\RichEditor;

class CobaPage extends Page
{
    protected static string $resource = KategoriPengajuanResource::class;
    protected static ?string $navigationLabel = 'Pengaturan Berita Acara';
    protected static ?string $title = 'Pengaturan Berita Acara';


    protected static string $view = 'filament.resources.kategori-pengajuan-resource.pages.coba-page';

    use Forms\Concerns\InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $setting = AcaraSetting::where(
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
                Forms\Components\Textarea::make('alamat')
                    ->label('Alamat Lengkap')
                    ->rows(4),
                Forms\Components\Textarea::make('kontak')
                    ->label('Kontak')
                    ->rows(4),
                RichEditor::make('header')
                    ->label('Header')
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
                RichEditor::make('footer')
                    ->label('Footer')
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
                TextInput::make('company_id')
                    ->readonly()
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

            AcaraSetting::updateOrCreate(
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
