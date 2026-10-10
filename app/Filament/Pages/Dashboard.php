<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\LampiranMcu;
use App\Models\LogAudit;
use App\Models\Pasien;
use App\Models\PemeriksaanMcu;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as FilamentDashboard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Dashboard extends FilamentDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'Generator MCU';

    public function getTitle(): string
    {
        $nama = Auth::user()?->name ?? 'Operator';
        return "Halo, {$nama}! 👋";
    }

    protected static string $view = 'filament.pages.dashboard';

    public ?array $data = [];
    public ?string $activeUuid = null;

    public function mount(): void
    {
        $this->activeUuid = Str::uuid()->toString();
        $this->form->fill([
            'format_qr' => 'png',
            'ukuran_qr' => '1000',
            'status_kelayakan' => 'FIT',
            'tanggal_pemeriksaan' => now()->toDateString(),
            'tanggal_kedaluwarsa' => now()->addYears(2)->toDateString(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(12)->schema([
                    // KOLOM KIRI: Form Utama (8/12)
                    Group::make()->schema([
                        Section::make('1. Data Sertifikat MCU')
                            ->description('Isi data pasien sesuai hasil pemeriksaan laboratorium medis.')
                            ->icon('heroicon-m-identification')
                            ->schema([
                                TextInput::make('nama_pasien')
                                    ->label('Nama Lengkap (Full Name)')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(debounce: 500),

                                Grid::make(2)->schema([
                                    Select::make('jenis_kelamin')
                                        ->label('Jenis Kelamin (Gender)')
                                        ->options([
                                            'Laki-laki' => 'Laki-laki (Male)',
                                            'Perempuan' => 'Perempuan (Female)',
                                        ])
                                        ->required()
                                        ->native(false),

                                    DatePicker::make('tanggal_lahir')
                                        ->label('Tanggal Lahir (Date of Birth)')
                                        ->required()
                                        ->maxDate(now())
                                        ->native(false),
                                ]),

                                Grid::make(2)->schema([
                                    TextInput::make('no_ktp')
                                        ->label('Nomor KTP / NIK (National ID)')
                                        ->maxLength(30),

                                    TextInput::make('no_paspor')
                                        ->label('Nomor Paspor (Passport) - Opsional')
                                        ->maxLength(30),
                                ]),

                                TextInput::make('nama_perusahaan')
                                    ->label('Perusahaan / Instansi (Company)')
                                    ->maxLength(255),

                                Grid::make(2)->schema([
                                    DatePicker::make('tanggal_pemeriksaan')
                                        ->label('Tanggal Pemeriksaan MCU')
                                        ->default(now()->toDateString())
                                        ->required()
                                        ->native(false),

                                    DatePicker::make('tanggal_kedaluwarsa')
                                        ->label('Berlaku Sampai (Valid Until)')
                                        ->default(now()->addYears(2)->toDateString())
                                        ->required()
                                        ->native(false),
                                ]),

                                ToggleButtons::make('status_kelayakan')
                                    ->label('Status Kelayakan (Fitness Status)')
                                    ->options([
                                        'FIT' => 'LAYAK KERJA (FIT)',
                                        'UNFIT' => 'TIDAK LAYAK KERJA (UNFIT)',
                                    ])
                                    ->colors([
                                        'FIT' => 'success',
                                        'UNFIT' => 'danger',
                                    ])
                                    ->icons([
                                        'FIT' => 'heroicon-m-check-badge',
                                        'UNFIT' => 'heroicon-m-x-circle',
                                    ])
                                    ->inline()
                                    ->default('FIT')
                                    ->required(),
                            ]),

                        Section::make('2. Berkas Lampiran MCU (PDF / Gambar Medis)')
                            ->description('Unggah hasil cetak laboratorium medis PDF atau scan dokumen (JPG/PNG). Lampiran otomatis dienkripsi dan diarsip.')
                            ->icon('heroicon-m-document-arrow-up')
                            ->schema([
                                FileUpload::make('berkas_lampiran')
                                    ->hiddenLabel()
                                    ->disk('public')
                                    ->directory('lampiran-mcu/temp')
                                    ->multiple()
                                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(15360), // 15 MB
                            ]),
                    ])->columnSpan(['default' => 12, 'lg' => 8]),

                    // KOLOM KANAN: Pratinjau & Kontrol QR (4/12)
                    Group::make()->schema([
                        Section::make('3. Pratinjau & Unduh QR Code')
                            ->icon('heroicon-m-qr-code')
                            ->schema([
                                Placeholder::make('qr_preview')
                                    ->hiddenLabel()
                                    ->content(function (Get $get) {
                                        if ($get('nama_pasien')) {
                                            $url = route('mcu.verify', ['uuid' => $this->activeUuid]);

                                            $builder = Builder::create()
                                                ->writer(new SvgWriter())
                                                ->data($url)
                                                ->encoding(new Encoding('UTF-8'))
                                                ->errorCorrectionLevel(ErrorCorrectionLevel::High)
                                                ->size(220)
                                                ->margin(5)
                                                ->roundBlockSizeMode(RoundBlockSizeMode::Margin);

                                            $svg = $builder->build()->getString();

                                            return new HtmlString('
                                                <div wire:loading.remove wire:target="data" class="flex flex-col items-center justify-center p-4 bg-white dark:bg-slate-900 rounded-xl border-2 border-dashed border-primary-500 shadow-sm transition-all duration-300">
                                                    ' . $svg . '
                                                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400 font-mono text-center break-all">' . e($url) . '</p>
                                                </div>
                                                <div wire:loading wire:target="data" class="w-full">
                                                    <div class="flex flex-col justify-center items-center p-8 bg-slate-50 dark:bg-slate-900 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-700 animate-pulse">
                                                        <svg class="w-10 h-10 text-primary-500 mb-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                        <span class="text-sm font-medium text-primary-600">Sinkronisasi Database...</span>
                                                    </div>
                                                </div>
                                            ');
                                        }

                                        return new HtmlString('
                                            <div wire:loading.remove wire:target="data" class="flex flex-col justify-center items-center p-8 bg-slate-50 dark:bg-slate-900 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-700">
                                                <svg class="w-12 h-12 text-slate-400 mb-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Silakan masukkan Nama Pasien...</span>
                                            </div>
                                            <div wire:loading wire:target="data" class="w-full">
                                                <div class="flex flex-col justify-center items-center p-8 bg-slate-50 dark:bg-slate-900 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-700 animate-pulse">
                                                    <svg class="w-10 h-10 text-primary-500 mb-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                    <span class="text-sm font-medium text-primary-600">Sinkronisasi...</span>
                                                </div>
                                            </div>
                                        ');
                                    }),

                                Grid::make(2)->schema([
                                    Select::make('format_qr')
                                        ->label('Format Berkas')
                                        ->options([
                                            'png' => 'PNG (Raster HD)',
                                            'svg' => 'SVG (Vektor Cetak)',
                                        ])
                                        ->default('png')
                                        ->native(false)
                                        ->required(),

                                    Select::make('ukuran_qr')
                                        ->label('Ukuran Resolusi')
                                        ->options([
                                            '500' => '500 px (Kecil)',
                                            '1000' => '1000 px (Standar)',
                                            '2000' => '2000 px (Cetak Tebal)',
                                        ])
                                        ->default('1000')
                                        ->native(false)
                                        ->required(),
                                ]),

                                Actions::make([
                                    Action::make('download_action')
                                        ->label('UNDUH QR CODE MCU')
                                        ->icon('heroicon-s-arrow-down-tray')
                                        ->size('lg')
                                        ->color('primary')
                                        ->disabled(fn (Get $get) => empty($get('nama_pasien')))
                                        ->action('processDownload')
                                        ->extraAttributes(['class' => 'w-full [&>button]:w-full [&>button]:justify-center shadow-md']),
                                ])->fullWidth(),
                            ])
                            ->extraAttributes(['class' => 'sticky top-6 shadow-sm']),
                    ])->columnSpan(['default' => 12, 'lg' => 4]),
                ]),
            ])
            ->statePath('data');
    }

    public function syncArchiveToDatabase(): void
    {
        $state = $this->form->getState();
        if (empty($state['nama_pasien'])) {
            return;
        }

        DB::transaction(function () use ($state): void {
            // 1. Cari atau buat entitas Pasien
            $pasien = null;
            if (!empty($state['no_ktp'])) {
                $pasien = Pasien::where('no_ktp', $state['no_ktp'])->first();
            } elseif (!empty($state['no_paspor'])) {
                $pasien = Pasien::where('no_paspor', $state['no_paspor'])->first();
            }

            if (!$pasien) {
                $pasien = Pasien::where('nama', $state['nama_pasien'])->first();
            }

            $atributPasien = [
                'nama' => (string) $state['nama_pasien'],
                'jenis_kelamin' => $state['jenis_kelamin'] ?? null,
                'tanggal_lahir' => $state['tanggal_lahir'] ?? null,
                'no_ktp' => $state['no_ktp'] ?? null,
                'no_paspor' => $state['no_paspor'] ?? null,
            ];

            if (!$pasien) {
                $pasien = Pasien::create($atributPasien);
            } else {
                $pasien->update(array_filter($atributPasien, fn ($val) => !is_null($val)));
            }

            // 2. Status kelayakan
            $statusKelayakan = ($state['status_kelayakan'] ?? 'FIT') === 'UNFIT' ? 'UNFIT' : 'FIT';

            // 3. Cari atau buat PemeriksaanMcu
            $pemeriksaan = PemeriksaanMcu::where('token_verifikasi', $this->activeUuid)->first();

            $dataPemeriksaan = [
                'pasien_id' => $pasien->id,
                'tanggal_periksa' => $state['tanggal_pemeriksaan'] ?? $state['tanggal_periksa'] ?? now()->toDateString(),
                'berlaku_sampai' => $state['tanggal_kedaluwarsa'] ?? $state['berlaku_sampai'] ?? null,
                'nama_perusahaan' => $state['nama_perusahaan'] ?? null,
                'status_kelayakan' => $statusKelayakan,
                'token_verifikasi' => $this->activeUuid,
                'dibuat_oleh' => Auth::id() ?? 1,
            ];

            if (!$pemeriksaan) {
                $dataPemeriksaan['nomor_mcu'] = app(\App\Actions\Mcu\GenerateNomorMcuAction::class)->execute((int) date('Y'));
                $pemeriksaan = PemeriksaanMcu::create($dataPemeriksaan);

                LogAudit::rekam(
                    aksi: 'pemeriksaan_mcu_dibuat',
                    model: $pemeriksaan,
                    rincian: [
                        'nomor_mcu' => $pemeriksaan->nomor_mcu,
                        'pasien_id' => $pasien->id,
                        'nama_pasien' => $pasien->nama,
                    ]
                );
            } else {
                $pemeriksaan->update($dataPemeriksaan);

                LogAudit::rekam(
                    aksi: 'pemeriksaan_mcu_diperbarui',
                    model: $pemeriksaan,
                    rincian: [
                        'nomor_mcu' => $pemeriksaan->nomor_mcu,
                        'pasien_id' => $pasien->id,
                        'nama_pasien' => $pasien->nama,
                    ]
                );
            }

            // 4. Proses lampiran berkas
            if (!empty($state['berkas_lampiran'])) {
                $berkasList = is_array($state['berkas_lampiran']) ? $state['berkas_lampiran'] : [$state['berkas_lampiran']];
                $direktoriTujuan = 'lampiran-mcu/' . date('Y/m');

                foreach ($berkasList as $indeks => $pathSementara) {
                    if (!is_string($pathSementara)) {
                        continue;
                    }

                    $ekstensi = pathinfo($pathSementara, PATHINFO_EXTENSION) ?: 'pdf';
                    $namaFileBersih = $this->activeUuid . '_' . ($indeks + 1) . '.' . $ekstensi;
                    $pathPermanen = $direktoriTujuan . '/' . $namaFileBersih;

                    if (Storage::disk('public')->exists($pathSementara)) {
                        if (!str_starts_with($pathSementara, 'lampiran-mcu/' . date('Y/m'))) {
                            if (Storage::disk('public')->exists($pathPermanen)) {
                                Storage::disk('public')->delete($pathPermanen);
                            }
                            Storage::disk('public')->copy($pathSementara, $pathPermanen);
                        } else {
                            $pathPermanen = $pathSementara;
                        }

                        $fullPath = Storage::disk('public')->path($pathPermanen);
                        $ukuranBerkas = file_exists($fullPath) ? filesize($fullPath) : (Storage::disk('public')->size($pathPermanen) ?: 0);
                        $tipeMime = Storage::disk('public')->mimeType($pathPermanen) ?: 'application/pdf';
                        $hashSha256 = file_exists($fullPath) ? hash_file('sha256', $fullPath) : null;

                        LampiranMcu::updateOrCreate(
                            [
                                'pemeriksaan_mcu_id' => $pemeriksaan->id,
                                'path_berkas' => $pathPermanen,
                            ],
                            [
                                'nama_berkas' => basename($pathPermanen),
                                'tipe_mime' => $tipeMime,
                                'ukuran_berkas' => $ukuranBerkas,
                                'jenis_lampiran' => str_contains($tipeMime, 'image') ? 'hasil_laboratorium' : 'sertifikat',
                                'hash_sha256' => $hashSha256,
                                'diunggah_oleh' => Auth::id() ?? 1,
                            ]
                        );
                    }
                }
            }
        });
    }

    public function processDownload()
    {
        $state = $this->form->getState();

        if (empty($state['nama_pasien'])) {
            Notification::make()->title('Gagal: Nama Pasien wajib diisi terlebih dahulu!')->danger()->send();
            return null;
        }

        try {
            // Pastikan data tersimpan secara atomik saat unduh ditekan
            $this->syncArchiveToDatabase();

            $urlTujuan = route('mcu.verify', ['uuid' => $this->activeUuid]);
            $format = $state['format_qr'] ?? 'png';
            $ukuran = (int) ($state['ukuran_qr'] ?? 1000);

            $writer = $format === 'svg' ? new SvgWriter() : new PngWriter();

            $builder = Builder::create()
                ->writer($writer)
                ->data($urlTujuan)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
                ->size($ukuran)
                ->margin(10)
                ->roundBlockSizeMode(RoundBlockSizeMode::Margin);

            $qrContent = $builder->build()->getString();
            $namaFileUnduhan = 'QR_MCU_' . Str::slug($state['nama_pasien']) . '_' . substr($this->activeUuid, 0, 8) . '.' . $format;

            // Reset token aktif untuk pembuatan berikutnya
            $this->activeUuid = Str::uuid()->toString();
            $this->form->fill([
                'format_qr' => $format,
                'ukuran_qr' => (string) $ukuran,
                'status_kelayakan' => 'FIT',
                'tanggal_pemeriksaan' => now()->toDateString(),
                'tanggal_kedaluwarsa' => now()->addYears(2)->toDateString(),
            ]);

            Notification::make()->title("Sertifikat MCU untuk {$state['nama_pasien']} berhasil diterbitkan.")->success()->send();

            return response()->streamDownload(fn () => print($qrContent), $namaFileUnduhan);
        } catch (\Throwable $e) {
            Notification::make()->title('Terjadi Kesalahan')->body($e->getMessage())->danger()->send();
            return null;
        }
    }
}
