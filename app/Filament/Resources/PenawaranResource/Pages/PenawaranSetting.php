<?php

namespace App\Filament\Resources\PenawaranResource\Pages;

use Filament\Forms;
use Filament\Actions;;

use Filament\Forms\Form;

use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Hidden;
// use App\Models\PenawaranSetting;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Filament\Resources\PenawaranResource;
use App\Models\PenawaranSetting as PenawaranSettingModel;

class PenawaranSetting extends Page implements Forms\Contracts\HasForms
{

    public ?array $data = [];

    public function mount(): void
    {
        // $this->form->fill();
        $this->form->fill(
            PenawaranSettingModel::first()?->toArray() ?? []
        );
    }
    use Forms\Concerns\InteractsWithForms;

    protected static string $resource = PenawaranResource::class;

    protected static string $view = 'filament.resources.penawaran-resource.pages.penawaran-setting';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_perusahaan')
                    ->label('Nama Perusahaan'),
                Forms\Components\Textarea::make('alamat')
                    ->label('Alamat Lengkap')
                    ->rows(4),
                TextInput::make('telepon')
                    ->label('Nomor Telepon'),
                TextInput::make('mobile')
                    ->label('Nomor Mobile'),
                TextInput::make('email')
                    ->label('Email Perusahaan'),
                Forms\Components\Textarea::make('catatan')
                    ->label('Catatan')
                    ->rows(4),
                Forms\Components\Textarea::make('penutup')
                    ->label('Penutup')
                    ->rows(4),
                TextInput::make('signature_name')
                    ->label('Signature'),
                TextInput::make('nama')
                    ->label('Nama Penandatangan'),
                TextInput::make('jabatan')
                    ->label('Jabatan Penandatangan'),
                TextInput::make('company_id')
                    ->default(fn() => \Filament\Facades\Filament::getTenant()?->getKey())
                    ->reactive()
                    ->helperText('Akan otomatis terisi dengan Company ID dari tenant yang sedang aktif'),
                // ->default('haaha'),
            ])
            ->statePath('data');
    }

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Action::make('edit')
    //             ->url(route('penawaran.edit', ['header_title' => $this->header_title])),
    //         Action::make('delete')
    //             ->requiresConfirmation()
    //             ->action(fn() => $this->header_title->delete()),
    //     ];
    // }

    // public function save()
    // {
    //     foreach ($this->data as $key => $value) {
    //         \App\Models\Penawaran::updateOrCreate(
    //             ['key' => "penawaran.$key"],
    //             ['value' => $value]
    //         );
    //     }

    //     $this->notify('success', 'Setting berhasil disimpan');
    // }

    public function save()
    {
        try {

            $setting = PenawaranSettingModel::first();

            if ($setting) {
                $setting->update($this->data);
            } else {
                PenawaranSettingModel::create($this->data);
            }
            // $data = $this->form->getState();

            // $this->notify('success', 'Setting berhasil disimpan');
        } catch (\Exception $e) {
            Notification::make()
                ->title('Setting gagal' . $e->getMessage())
                ->danger()
                ->send();
            // $this->notify('danger', 'Terjadi kesalahan saat menyimpan setting: ' . $e->getMessage());
        }
        Notification::make()
            ->title('Setting berhasil disimpan')
            ->success()
            ->send();
    }

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Action::make(),
    //         Actions\EditAction::make('hasllo')
    //     ];
    // }

    // public function getFormAction()
    // {
    //     return [
    //         Action::make()
    //             ->submit('save')
    //             ->label('HAHA')
    //         // ->action('save')
    //     ];
    // }
}
