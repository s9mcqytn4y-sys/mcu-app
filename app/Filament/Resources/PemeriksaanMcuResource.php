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
                        // Kolom Kiri (8 Kolom): Section 1 (Data MCU) & Section 2 (Lampiran)
                        Grid::make(1)
                            ->columnSpan(['default' => 1, 'lg' => 8])
                            ->schema([
                                Section::make('1. Data Sertifikat MCU')
                                    ->description('Isi data pasien dan hasil pemeriksaan MCU secara lengkap.')
                                    ->icon('heroicon-o-document-text')
                                    ->collapsible()
                                    ->schema([
                                        Grid::make(['default' => 1, 'md' => 2])->schema([
                                            TextInput::make('nama_pasien')
                                                ->label('Nama Lengkap (Name)')
                                                ->required()
                                                ->maxLength(255)
                                                ->placeholder('Contoh: AZIZ')
                                                ->autofocus()
                                                ->autocomplete('off')
                                                ->live(debounce: 1000),

                                            Select::make('jenis_kelamin')
                                                ->label('Jenis Kelamin (Gender)')
                                                ->options([
                                                    'Laki-laki' => 'Laki-laki (Male)',
                                                    'Perempuan' => 'Perempuan (Female)',
                                                ])
                                                ->required()
                                                ->native(false),
                                        ]),

                                        Grid::make(['default' => 1, 'md' => 2])->schema([
                                            DatePicker::make('tanggal_lahir')
                                                ->label('Tanggal Lahir (Date of Birth)')
                                                ->required()
                                                ->maxDate(now())
                                                ->displayFormat('d/m/Y')
                                                ->closeOnDateSelection()
                                                ->native(false),

                                            TextInput::make('no_ktp')
                                                ->label('KTP (ID Number)')
                                                ->required()
                                                ->length(16)
                                                ->regex('/^[0-9]{16}$/')
                                                ->validationMessages([
                                                    'regex' => 'Nomor KTP wajib tepat 16 digit angka.',
                                                ])
                                                ->placeholder('16 Digit NIK')
                                                ->autocomplete('off')
                                                ->extraInputAttributes(['inputmode' => 'numeric', 'pattern' => '[0-9]*'])
                                                ->helperText('Jika NIK sudah ada, data pasien otomatis diperbarui.'),
                                        ]),

                                        Grid::make(['default' => 1, 'md' => 2])->schema([
                                            TextInput::make('no_paspor')
                                                ->label('Paspor (Passport) - Opsional')
                                                ->maxLength(30)
                                                ->autocomplete('off')
                                                ->placeholder('Nomor Paspor Aktif'),

                                            TextInput::make('nama_perusahaan')
                                                ->label('Perusahaan (Company)')
                                                ->required()
                                                ->maxLength(255)
                                                ->autocomplete('off')
                                                ->placeholder('PT / Perusahaan Pemohon'),
                                        ]),

                                        Grid::make(['default' => 1, 'md' => 2])->schema([
                                            DatePicker::make('tanggal_periksa')
                                                ->label('Tanggal MCU (Date of Checkup)')
                                                ->required()
                                                ->default(now()->toDateString())
                                                ->displayFormat('d/m/Y')
                                                ->closeOnDateSelection()
                                                ->native(false),

                                            DatePicker::make('berlaku_sampai')
                                                ->label('Berlaku Sampai (Valid Until)')
                                                ->required()
                                                ->default(now()->addYears(2)->toDateString())
                                                ->displayFormat('d/m/Y')
                                                ->closeOnDateSelection()
                                                ->native(false),
                                        ]),

                                        ToggleButtons::make('status_kelayakan')
                                            ->label('Status Kelayakan (Fitness Status)')
                                            ->options([
                                                'FIT' => 'FIT (LAYAK KERJA)',
                                                'UNFIT' => 'UNFIT (TIDAK LAYAK KERJA)',
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
                                            ->required()
                                            ->live(),
                                    ]),

                                Section::make('2. Lampiran File (Opsional)')
                                    ->description('Unggah hasil cetak PDF atau foto dokumen (JPG/PNG). Berkas akan ditumpuk di bawah sertifikat cover.')
                                    ->icon('heroicon-o-paper-clip')
                                    ->collapsible()
                                    ->schema([
                                        FileUpload::make('lampiran_file')
                                            ->label('Unggah Berkas PDF / Foto Hasil Cetak (JPG/PNG)')
                                            ->disk('local')
                                            ->directory('lampiran-mcu')
                                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                            ->maxSize(20480)
                                            ->helperText('Format PDF, JPG, atau PNG (Maks. 20MB). Berkas akan dirender tepat di bawah lembar cover sertifikat MCU.'),
                                    ]),
                            ]),

                        // Kolom Kanan (4 Kolom): Section 3 (Pratinjau Langsung & QR Code) - Sticky di Desktop
                        Grid::make(1)
                            ->columnSpan(['default' => 1, 'lg' => 4])
                            ->schema([
                                Section::make('3. Pratinjau Sertifikat & QR')
                                    ->description('QR Code real-time yang dapat discan saat sertifikat diterbitkan.')
                                    ->icon('heroicon-o-qr-code')
                                    ->extraAttributes(['class' => 'lg:sticky lg:top-4'])
                                    ->schema([
                                        \Filament\Forms\Components\View::make('filament.components.mcu-live-preview')
                                            ->viewData(fn (Get $get, ?PemeriksaanMcu $record): array => [
                                                'record' => $record,
                                                'nama' => (string) ($get('nama_pasien') ?: ($record?->pasien?->nama ?? '-')),
                                                'perusahaan' => (string) ($get('nama_perusahaan') ?: ($record?->nama_perusahaan ?? '-')),
                                                'status' => (string) ($get('status_kelayakan') ?: ($record?->status_kelayakan ?? 'FIT')),
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
                    ->color(fn (string $state): string => match ($state) {
                        'FIT' => 'success',
                        'UNFIT' => 'danger',
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
                        'FIT' => 'FIT',
                        'UNFIT' => 'UNFIT',
                    ]),
            ])
            ->actions([
                \Filament\Tables\Actions\ActionGroup::make([
                    Action::make('lihat_publik')
                        ->label('Buka Portal Publik')
                        ->icon('heroicon-m-arrow-top-right-on-square')
                        ->color('gray')
                        ->url(fn (PemeriksaanMcu $record): string => route('mcu.verify', ['uuid' => $record->token_verifikasi]))
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
                                fn () => print($png),
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
                                fn () => print($svg),
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
                        ->visible(fn (): bool => (\Illuminate\Support\Facades\Auth::user() instanceof \App\Models\User) && \Illuminate\Support\Facades\Auth::user()->isAdministrator()),
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
                    ->visible(fn (): bool => (\Illuminate\Support\Facades\Auth::user() instanceof \App\Models\User) && \Illuminate\Support\Facades\Auth::user()->isAdministrator()),
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
