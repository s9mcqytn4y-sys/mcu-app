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
                Wizard::make([
                    Wizard\Step::make('Identitas Pasien')
                        ->description('Pilih pasien terdaftar atau daftarkan pasien baru')
                        ->icon('heroicon-m-user')
                        ->schema([
                            Select::make('pasien_id')
                                ->label('Pasien Terdaftar')
                                ->relationship('pasien', 'nama')
                                ->searchable(['nama', 'no_ktp', 'no_paspor'])
                                ->getOptionLabelFromRecordUsing(fn (\App\Models\Pasien $record): string => "{$record->nama} (NIK: " . ($record->no_ktp ?? '-') . ' | Paspor: ' . ($record->no_paspor ?? '-') . ')')
                                ->preload()
                                ->required()
                                ->createOptionForm([
                                    TextInput::make('nama')
                                        ->label('Nama Lengkap')
                                        ->required()
                                        ->maxLength(255),
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
                                        ->maxDate(now())
                                        ->native(false),
                                    TextInput::make('no_ktp')
                                        ->label('Nomor KTP / NIK')
                                        ->maxLength(30)
                                        ->unique(\App\Models\Pasien::class, 'no_ktp'),
                                    TextInput::make('no_paspor')
                                        ->label('Nomor Paspor')
                                        ->maxLength(30)
                                        ->unique(\App\Models\Pasien::class, 'no_paspor'),
                                ])
                                ->createOptionModalHeading('Daftarkan Pasien Baru')
                                ->helperText('Cari pasien berdasarkan Nama, NIK, atau Nomor Paspor. Jika belum ada, klik tanda "+" untuk input baru.')
                                ->native(false),
                        ]),

                    Wizard\Step::make('Pemeriksaan Klinis')
                        ->description('Nomor sertifikat, tanggal berlaku, dan kelayakan kerja')
                        ->icon('heroicon-m-clipboard-document-check')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('nomor_mcu')
                                    ->label('Nomor Sertifikat MCU')
                                    ->required()
                                    ->maxLength(100)
                                    ->unique(ignoreRecord: true)
                                    ->default(fn () => app(\App\Actions\Mcu\GenerateNomorMcuAction::class)->generatePreview())
                                    ->helperText('Nomor MCU resmi diterbitkan berurutan secara transaksional.')
                                    ->readOnly(),

                                TextInput::make('nama_perusahaan')
                                    ->label('Perusahaan / Instansi Pemohon')
                                    ->placeholder('PT Pelayaran / Perusahaan Pemohon')
                                    ->maxLength(255),
                            ]),

                            Grid::make(2)->schema([
                                DatePicker::make('tanggal_periksa')
                                    ->label('Tanggal Pemeriksaan')
                                    ->required()
                                    ->default(now()->toDateString())
                                    ->native(false),

                                DatePicker::make('berlaku_sampai')
                                    ->label('Masa Berlaku Sampai')
                                    ->required()
                                    ->default(now()->addYears(2)->toDateString())
                                    ->native(false),
                            ]),

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

                            ToggleButtons::make('status_kelayakan')
                                ->label('Status Kelayakan Medis (Medical Fitness)')
                                ->options([
                                    'FIT' => 'LAYAK KERJA (FIT FOR SEA DUTY)',
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

                            TextInput::make('catatan')
                                ->label('Catatan Dokter / Keterangan Klinis')
                                ->placeholder('Contoh: Memerlukan kacamata koreksi saat bertugas')
                                ->maxLength(500),
                        ]),

                    Wizard\Step::make('Lampiran Berkas Medis')
                        ->description('Unggah hasil laboratorium atau berkas penunjang ke Private Storage')
                        ->icon('heroicon-m-paper-clip')
                        ->schema([
                            Repeater::make('lampiranMcu')
                                ->relationship('lampiranMcu')
                                ->label('Dokumen & Berkas Penunjang')
                                ->schema([
                                    FileUpload::make('path_berkas')
                                        ->label('Berkas Medis (PDF, JPG, PNG)')
                                        ->disk('local')
                                        ->directory('lampiran-mcu')
                                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                        ->maxSize(10240)
                                        ->required(),

                                    Select::make('jenis_lampiran')
                                        ->label('Kategori Lampiran')
                                        ->options([
                                            'sertifikat' => 'Sertifikat Kelayakan Resmi',
                                            'laporan_medis' => 'Hasil Laboratorium & Diagnostik',
                                            'berkas_tambahan' => 'Rontgen / EKG / Lainnya',
                                        ])
                                        ->default('sertifikat')
                                        ->required()
                                        ->native(false),
                                ])
                                ->columns(2)
                                ->defaultItems(0)
                                ->addActionLabel('Tambah Lampiran Berkas')
                                ->helperText('Berkas disimpan secara privat di disk terisolasi (tidak dapat diakses publik tanpa verifikasi sah).'),
                        ]),

                    Wizard\Step::make('Konfirmasi & Publikasi')
                        ->description('Verifikasi akhir sebelum penerbitan sertifikat QR resmi')
                        ->icon('heroicon-m-shield-check')
                        ->schema([
                            Placeholder::make('ringkasan_konfirmasi')
                                ->label('Ketentuan Penerbitan Dokumen')
                                ->content('Pastikan seluruh data identitas pasien dan hasil kelayakan klinis telah diperiksa dengan seksama. Setelah menekan tombol Simpan / Buat, sertifikat MCU akan diterbitkan dengan token verifikasi kriptografis unik dan dicatat dalam Log Audit.'),
                        ]),
                ])->columnSpanFull(),
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
                    ->visible(fn (): bool => auth()->user()?->isAdministrator() ?? false),
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
