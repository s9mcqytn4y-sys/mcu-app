<?php

namespace App\Filament\Pages;

use App\Models\McuExamination;
use App\Models\Patient;
use Carbon\Carbon;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class McuQrGenerator extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $navigationLabel = 'QR Generator MCU';

    protected static ?string $title = 'Generate & Export QR Code';

    protected static string $view = 'filament.pages.mcu-qr-generator';

    // State properties
    public ?string $patient_name = null;

    public ?string $mcu_number = null;

    public $scan_images = [];

    public string $qr_format = 'png';

    public int $qr_size = 1000;

    // Virtual property for preview
    public ?string $generatedUrl = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Pasien & Validitas')
                    ->description('Masukkan detail informasi pasien untuk dokumen ini.')
                    ->schema([
                        TextInput::make('patient_name')
                            ->label('Nama Pasien')
                            ->required()
                            ->live(debounce: 500)
                            ->afterStateUpdated(fn () => $this->updatePreview()),
                        TextInput::make('mcu_number')
                            ->label('Nomor MCU')
                            ->required()
                            ->live(debounce: 500)
                            ->afterStateUpdated(fn () => $this->updatePreview()),
                    ]),

                Section::make('Upload Hasil MCU')
                    ->description('Unggah 1 atau 2 lembar hasil rekam medis (Status FIT).')
                    ->schema([
                        FileUpload::make('scan_images')
                            ->label('Dokumen Medis (JPG, PNG, PDF)')
                            ->multiple()
                            ->maxFiles(2)
                            ->maxSize(2048) // 2MB
                            ->optimize('webp')
                            ->directory('mcu-scans')
                            ->required(),
                    ]),
            ])
            ->statePath('');
    }

    public function updatePreview()
    {
        if ($this->mcu_number && $this->patient_name) {
            // Simulasi token untuk preview
            $previewToken = Str::uuid()->toString();
            $this->generatedUrl = route('mcu.verify', ['token' => $previewToken]);
        } else {
            $this->generatedUrl = null;
        }
    }

    public function downloadQrCode()
    {
        $data = $this->form->getState();

        // 1. Simpan ke database
        $token = Str::uuid()->toString();

        $patient = Patient::firstOrCreate(
            ['name' => $data['patient_name']],
            [
                'date_of_birth' => Carbon::now()->subYears(30)->toDateString(),
                'gender' => 'MALE',
                'id_number' => 'DUMMY'.rand(1000, 9999),
            ]
        );

        $mcu = McuExamination::create([
            'patient_id' => $patient->id,
            'mcu_number' => $data['mcu_number'],
            'verification_token' => $token,
            'scan_images' => $data['scan_images'],
            'examination_date' => now(),
            'expiration_date' => now()->addYears(2),
            'fitness_status' => 'FIT',
            'doctor_name' => 'dr. WIDHA PUJI ISMAYAWATI',
        ]);

        $finalUrl = route('mcu.verify', ['token' => $token]);

        // 2. Generate QR
        $qr = QrCode::format($this->qr_format)
            ->size($this->qr_size)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($finalUrl);

        // 3. Notifikasi Sukses
        Notification::make()
            ->title('QR Code Berhasil Diunduh!')
            ->body('Silakan tempelkan pada dokumen fisik MCU.')
            ->success()
            ->send();

        // Reset form after successful save and download
        $this->form->fill();
        $this->generatedUrl = null;

        // 4. Return Download Response
        $filename = 'QR_'.Str::slug($data['patient_name']).'_'.$this->qr_size.'x'.$this->qr_size.'.'.$this->qr_format;

        return response()->streamDownload(fn () => print ($qr), $filename);
    }
}
