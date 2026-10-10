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
                Grid::make(['default' => 1, 'lg' => 3])
                    ->schema([
                        // Kolom Kiri (2 Kolom): Section 1 (Data MCU) & Section 2 (Lampiran)
                        Grid::make(1)
                            ->columnSpan(['default' => 1, 'lg' => 2])
                            ->schema([
                                Section::make('1. Data Sertifikat MCU')
                                    ->description('Isi data pasien dan hasil pemeriksaan MCU secara lengkap.')
                                    ->icon('heroicon-o-document-text')
                                    ->collapsible()
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('nama_pasien')
                                                ->label('Nama Lengkap (Name)')
                                                ->required()
                                                ->maxLength(255)
                                                ->placeholder('Contoh: AZIZ')
                                                ->live(debounce: 500),

                                            Select::make('jenis_kelamin')
                                                ->label('Jenis Kelamin (Gender)')
                                                ->options([
                                                    'Laki-laki' => 'Laki-laki (Male)',
                                                    'Perempuan' => 'Perempuan (Female)',
                                                ])
                                                ->required()
                                                ->native(false),
                                        ]),

                                        Grid::make(2)->schema([
                                            DatePicker::make('tanggal_lahir')
                                                ->label('Tanggal Lahir (Date of Birth)')
                                                ->required()
                                                ->maxDate(now())
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
                                                ->helperText('Jika NIK sudah ada, data pasien otomatis diperbarui.'),
                                        ]),

                                        Grid::make(2)->schema([
                                            TextInput::make('no_paspor')
                                                ->label('Paspor (Passport) - Opsional')
                                                ->maxLength(30)
                                                ->placeholder('Nomor Paspor Aktif'),

                                            TextInput::make('nama_perusahaan')
                                                ->label('Perusahaan (Company)')
                                                ->required()
                                                ->maxLength(255)
                                                ->placeholder('PT / Perusahaan Pemohon'),
                                        ]),

                                        Grid::make(2)->schema([
                                            DatePicker::make('tanggal_periksa')
                                                ->label('Tanggal MCU (Date of Checkup)')
                                                ->required()
                                                ->default(now()->toDateString())
                                                ->native(false),

                                            DatePicker::make('berlaku_sampai')
                                                ->label('Berlaku Sampai (Valid Until)')
                                                ->required()
                                                ->default(now()->addYears(2)->toDateString())
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

                                        Grid::make(3)->schema([
                                            TextInput::make('wilayah_operasi')
                                                ->label('Wilayah Operasi')
                                                ->placeholder('Near Coastal / Ocean Going')
                                                ->maxLength(100),

                                            TextInput::make('departemen')
                                                ->label('Departemen / Bagian')
                                                ->placeholder('Deck / Engine / Catering')
                                                ->maxLength(100),

                                            TextInput::make('jenis_kapal')
                                                ->label('Jenis Kapal')
                                                ->placeholder('Bulk Carrier / Oil Tanker')
                                                ->maxLength(100),
                                        ]),

                                        TextInput::make('catatan')
                                            ->label('Catatan Dokter / Klinis')
                                            ->placeholder('Contoh: Memerlukan kacamata koreksi saat dinas')
                                            ->maxLength(500),
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

                        // Kolom Kanan (1 Kolom): Section 3 (Pratinjau Langsung & QR Code)
                        Grid::make(1)
                            ->columnSpan(['default' => 1, 'lg' => 1])
                            ->schema([
                                Section::make('3. Pratinjau Langsung')
                                    ->description('QR Code real-time yang dapat discan saat sertifikat diterbitkan.')
                                    ->icon('heroicon-o-qr-code')
                                    ->schema([
                                        \Filament\Forms\Components\View::make('filament.components.mcu-live-preview'),
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
                    ->sortable(),

                TextColumn::make('nama_perusahaan')
                    ->label('Perusahaan')
                    ->searchable()
                    ->placeholder('-'),

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
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('berlaku_sampai')
                    ->label('Berlaku Hingga')
                    ->date('d M Y')
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
                Action::make('lihat_publik')
                    ->label('Portal Publik')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn (PemeriksaanMcu $record): string => route('mcu.verify', ['uuid' => $record->token_verifikasi]))
                    ->openUrlInNewTab(),

                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
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
