<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PemeriksaanMcuResource\Pages;
use App\Models\PemeriksaanMcu;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PemeriksaanMcuResource extends Resource
{
    protected static ?string $model = PemeriksaanMcu::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Pemeriksaan MCU';
    protected static ?string $modelLabel = 'Pemeriksaan MCU';
    protected static ?string $pluralModelLabel = 'Daftar Pemeriksaan MCU';
    protected static ?string $navigationGroup = 'Klinik & Operasional';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(['default' => 1, 'lg' => 12])
                    ->schema([
                        // Kolom Kiri (7 Kolom): Section 1 (Data MCU) & Section 2 (Lampiran)
                        Grid::make(1)
                            ->columnSpan(['default' => 1, 'lg' => 7, 'xl' => 7])
                            ->schema([
                                Section::make('1. Data Sertifikat MCU')
                                    ->description('Pilih pasien terdaftar atau daftarkan baru, lalu tentukan hasil evaluasi kelaikan medis maritim.')
                                    ->icon('heroicon-o-document-text')
                                    ->collapsible()
                                    ->schema([
                                        Select::make('pasien_id')
                                            ->label('Pilih Pasien Terdaftar')
                                            ->relationship('pasien', 'nama')
                                            ->searchable(['nama', 'no_ktp', 'no_paspor'])
                                            ->getOptionLabelFromRecordUsing(fn (\App\Models\Pasien $record): string => "{$record->nama} (NIK: {$record->no_ktp})")
                                            ->preload(false)
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $pasien = \App\Models\Pasien::find($state);
                                                    if ($pasien) {
                                                        $set('pasien_nama_display', $pasien->nama);
                                                        $set('pasien_nik_display', $pasien->no_ktp);
                                                        $set('pasien_jk_display', $pasien->jenis_kelamin);
                                                        $set('pasien_tgl_lahir_display', $pasien->tanggal_lahir?->format('d/m/Y'));
                                                        $set('pasien_paspor_display', $pasien->no_paspor ?? '-');
                                                    }
                                                }
                                            })
                                            ->createOptionForm([
                                                TextInput::make('nama')
                                                    ->label('Nama Lengkap')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('Contoh: Budi Santoso')
                                                    ->autocomplete('off'),
                                                Select::make('jenis_kelamin')
                                                    ->label('Jenis Kelamin')
                                                    ->options([
                                                        'Laki-laki' => 'Laki-laki',
                                                        'Perempuan' => 'Perempuan',
                                                    ])
                                                    ->required()
                                                    ->native(false),
                                                DatePicker::make('tanggal_lahir')
                                                    ->label('Tanggal Lahir')
                                                    ->required()
                                                    ->maxDate(now())
                                                    ->displayFormat('d/m/Y')
                                                    ->native(false),
                                                TextInput::make('no_ktp')
                                                    ->label('Nomor KTP (NIK)')
                                                    ->required()
                                                    ->length(16)
                                                    ->regex('/^[0-9]{16}$/')
                                                    ->unique('pasien', 'no_ktp')
                                                    ->validationMessages([
                                                        'regex' => 'Nomor KTP wajib tepat 16 digit angka.',
                                                        'unique' => 'NIK ini sudah terdaftar untuk pasien lain.',
                                                    ])
                                                    ->placeholder('16 digit NIK')
                                                    ->autocomplete('off')
                                                    ->extraInputAttributes(['inputmode' => 'numeric', 'pattern' => '[0-9]*']),
                                                TextInput::make('no_paspor')
                                                    ->label('Nomor Paspor (Opsional)')
                                                    ->maxLength(30)
                                                    ->placeholder('Nomor paspor aktif')
                                                    ->autocomplete('off'),
                                            ])
                                            ->createOptionAction(fn (\Filament\Forms\Components\Actions\Action $action) => $action
                                                ->modalHeading('Daftarkan Rekam Pasien Baru')
                                                ->modalSubmitActionLabel('Simpan Pasien')
                                            )
                                            ->helperText('Cari berdasarkan nama atau NIK. Jika pasien belum terdaftar, klik ikon (+) untuk menambahkan pasien baru tanpa menimpa data orang lain.'),

                                        // Ringkasan Identitas Pasien (Read-Only)
                                        Grid::make(['default' => 1, 'sm' => 2, 'md' => 4])
                                            ->visible(fn (Get $get, ?PemeriksaanMcu $record): bool => (bool) ($get('pasien_id') ?: $record?->pasien_id))
                                            ->schema([
                                                Placeholder::make('info_nik')
                                                    ->label('NIK Terdaftar')
                                                    ->content(fn (Get $get, ?PemeriksaanMcu $record): string => (string) ($get('pasien_nik_display') ?: ($record?->pasien?->no_ktp ?? '-'))),
                                                Placeholder::make('info_jk')
                                                    ->label('Jenis Kelamin')
                                                    ->content(fn (Get $get, ?PemeriksaanMcu $record): string => (string) ($get('pasien_jk_display') ?: ($record?->pasien?->jenis_kelamin ?? '-'))),
                                                Placeholder::make('info_tgl_lahir')
                                                    ->label('Tanggal Lahir')
                                                    ->content(fn (Get $get, ?PemeriksaanMcu $record): string => (string) ($get('pasien_tgl_lahir_display') ?: ($record?->pasien?->tanggal_lahir?->format('d/m/Y') ?? '-'))),
                                                Placeholder::make('info_paspor')
                                                    ->label('Nomor Paspor')
                                                    ->content(fn (Get $get, ?PemeriksaanMcu $record): string => (string) ($get('pasien_paspor_display') ?: ($record?->pasien?->no_paspor ?? '-'))),
                                            ]),

                                        Grid::make(['default' => 1, 'md' => 2])->schema([
                                            TextInput::make('nama_perusahaan')
                                                ->label('Nama Perusahaan / Kapal')
                                                ->required()
                                                ->maxLength(255)
                                                ->autocomplete('off')
                                                ->placeholder('PT Pelayaran / Nama Kapal')
                                                ->live(debounce: 1000),

                                            Select::make('status_kelayakan')
                                                ->label('Status Kelayakan Medis')
                                                ->options([
                                                    'FIT' => 'FIT',
                                                    'UNFIT' => 'UNFIT',
                                                    'Draf' => 'Draf',
                                                    'DICABUT' => 'Dicabut',
                                                ])
                                                ->default('FIT')
                                                ->required()
                                                ->native(false)
                                                ->disabled(function (?PemeriksaanMcu $record): bool {
                                                    if ($record === null) {
                                                        return false;
                                                    }
                                                    /** @var \App\Models\User|null $user */
                                                    $user = \Illuminate\Support\Facades\Auth::user();
                                                    $isPublished = in_array($record->status_kelayakan, ['FIT', 'UNFIT', 'DICABUT'], true);

                                                    return $isPublished && ($user === null || ! $user->apakahAdministrator());
                                                })
                                                ->helperText(function (?PemeriksaanMcu $record): ?string {
                                                    if ($record && in_array($record->status_kelayakan, ['FIT', 'UNFIT', 'DICABUT'], true)) {
                                                        return 'Status sertifikat terbit terkunci. Perubahan memerlukan izin Administrator melalui Revisi Klinis.';
                                                    }
                                                    return null;
                                                })
                                                ->live(),
                                        ]),

                                        Grid::make(['default' => 1, 'md' => 2])->schema([
                                            DatePicker::make('tanggal_periksa')
                                                ->label('Tanggal Pemeriksaan')
                                                ->required()
                                                ->default(now()->toDateString())
                                                ->displayFormat('d/m/Y')
                                                ->closeOnDateSelection()
                                                ->native(false),

                                            DatePicker::make('berlaku_sampai')
                                                ->label('Masa Berlaku Sertifikat')
                                                ->required()
                                                ->default(now()->addYears(2)->toDateString())
                                                ->displayFormat('d/m/Y')
                                                ->closeOnDateSelection()
                                                ->native(false),
                                        ]),
                                    ]),

                                Section::make('2. Lampiran File (Opsional)')
                                    ->description('Unggah hasil cetak berkas PDF atau foto dokumen (JPG/PNG).')
                                    ->icon('heroicon-o-paper-clip')
                                    ->collapsible()
                                    ->schema([
                                        FileUpload::make('lampiran_file')
                                            ->label('Berkas Rekam Medis / Hasil Cetak')
                                            ->disk('local')
                                            ->directory('lampiran-mcu')
                                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                            ->maxSize(20480)
                                            ->previewable(false)
                                            ->downloadable()
                                            ->openable()
                                            ->uploadingMessage('Mengunggah berkas medis...')
                                            ->helperText('Format PDF, JPG, atau PNG (Maks. 20MB). Berkas tersimpan di penyimpanan terlindung.'),
                                    ]),
                            ]),

                        // Kolom Kanan (5 Kolom): Section 3 (Pratinjau Langsung & QR Code) - Sticky di Desktop
                        Grid::make(1)
                            ->columnSpan(['default' => 1, 'lg' => 5, 'xl' => 5])
                            ->schema([
                                Section::make('3. Pratinjau Sertifikat & QR')
                                    ->description('Kode QR real-time diterbitkan resmi saat sertifikat disimpan.')
                                    ->icon('heroicon-o-qr-code')
                                    ->extraAttributes(['class' => 'lg:sticky lg:top-4'])
                                    ->schema([
                                        \Filament\Forms\Components\View::make('filament.components.mcu-live-preview')
                                            ->viewData(fn(Get $get, ?PemeriksaanMcu $record): array => [
                                                'record' => $record,
                                                'nama' => (string) (
                                                    $get('pasien_nama_display')
                                                    ?: ($get('pasien_id') ? \App\Models\Pasien::find($get('pasien_id'))?->nama : null)
                                                    ?: ($record?->pasien?->nama ?? '-')
                                                ),
                                                'perusahaan' => (string) ($get('nama_perusahaan') ?: ($record?->nama_perusahaan ?? '-')),
                                                'status' => (string) ($get('status_kelayakan') ?: ($record?->status_kelayakan ?? 'DRAFT')),
                                                'verifyUrl' => $record?->token_verifikasi
                                                    ? route('mcu.verify', ['uuid' => $record->token_verifikasi])
                                                    : null,
                                            ]),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_mcu')
                    ->label('Nomor MCU')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('pasien.nama')
                    ->label('Pasien')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('nama_perusahaan')
                    ->label('Perusahaan')
                    ->searchable()
                    ->placeholder('-')
                    ->wrap(),

                TextColumn::make('status_kelayakan')
                    ->label('Kelayakan')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'FIT' => 'success',
                        'UNFIT' => 'danger',
                        'DRAFT' => 'warning',
                        'DICABUT' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('tanggal_periksa')
                    ->label('Tanggal MCU')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('berlaku_sampai')
                    ->label('Berlaku Hingga')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('lampiran_mcu_count')
                    ->counts('lampiranMcu')
                    ->label('Lampiran')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                SelectFilter::make('status_kelayakan')
                    ->label('Kelayakan')
                    ->options([
                        'DRAFT' => 'DRAFT',
                        'FIT' => 'FIT',
                        'UNFIT' => 'UNFIT',
                        'DICABUT' => 'DICABUT',
                    ]),
            ])
            ->actions([
                \Filament\Tables\Actions\ActionGroup::make([
                    Action::make('lihat_publik')
                        ->label('Buka Portal Publik')
                        ->icon('heroicon-m-arrow-top-right-on-square')
                        ->color('gray')
                        ->url(fn(PemeriksaanMcu $record): string => route('mcu.verify', ['uuid' => $record->token_verifikasi]))
                        ->openUrlInNewTab(),

                    Action::make('unduh_qr_png')
                        ->label('Unduh QR (PNG 1024px)')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function (PemeriksaanMcu $record) {
                            $qrAction = app(\App\Actions\Mcu\GenerateQrCodeAction::class);
                            $url = route('mcu.verify', ['uuid' => $record->token_verifikasi]);
                            $png = $qrAction->generatePngString($url, 1024);
                            $cleanNama = \Illuminate\Support\Str::slug($record->pasien->nama ?? 'pasien');
                            $cleanNomor = str_replace(['/', '\\'], '-', $record->nomor_mcu);
                            $filename = "QR-MCU-{$cleanNomor}-{$cleanNama}.png";

                            return response()->streamDownload(
                                fn() => print($png),
                                $filename,
                                ['Content-Type' => 'image/png']
                            );
                        }),

                    Action::make('unduh_qr_svg')
                        ->label('Unduh QR (Vektor SVG)')
                        ->icon('heroicon-o-code-bracket')
                        ->color('info')
                        ->action(function (PemeriksaanMcu $record) {
                            $qrAction = app(\App\Actions\Mcu\GenerateQrCodeAction::class);
                            $url = route('mcu.verify', ['uuid' => $record->token_verifikasi]);
                            $svg = $qrAction->generateSvgString($url, 512);
                            $cleanNama = \Illuminate\Support\Str::slug($record->pasien->nama ?? 'pasien');
                            $cleanNomor = str_replace(['/', '\\'], '-', $record->nomor_mcu);
                            $filename = "QR-MCU-{$cleanNomor}-{$cleanNama}.svg";

                            return response()->streamDownload(
                                fn() => print($svg),
                                $filename,
                                ['Content-Type' => 'image/svg+xml']
                            );
                        }),

                    EditAction::make()
                        ->label('Ubah Data'),

                    \Filament\Tables\Actions\DeleteAction::make()
                        ->label('Hapus')
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Hapus Data MCU')
                        ->modalDescription('Apakah Anda yakin ingin menghapus data pemeriksaan MCU ini? Tindakan ini tidak dapat dibatalkan.')
                        ->modalSubmitActionLabel('Ya, Hapus Data')
                        ->modalCancelActionLabel('Batal')
                        ->color('danger')
                        ->visible(function (): bool {
                            /** @var \App\Models\User|null $user */
                            $user = \Illuminate\Support\Facades\Auth::user();
                            return $user !== null && $user->apakahAdministrator();
                        }),
                ])
                    ->label('Aksi')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm'),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->label('Hapus Terpilih')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Hapus Massal Data MCU')
                    ->modalDescription('Apakah Anda yakin ingin menghapus data pemeriksaan MCU terpilih? Seluruh berkas lampiran terkait juga akan dihapus secara permanen.')
                    ->modalSubmitActionLabel('Ya, Hapus Semua Terpilih')
                    ->modalCancelActionLabel('Batal')
                    ->visible(function (): bool {
                        /** @var \App\Models\User|null $user */
                        $user = \Illuminate\Support\Facades\Auth::user();
                        return $user !== null && $user->apakahAdministrator();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPemeriksaanMcus::route('/'),
            'create' => Pages\CreatePemeriksaanMcu::route('/create'),
            'edit' => Pages\EditPemeriksaanMcu::route('/{record}/edit'),
        ];
    }
}
