<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PemeriksaanMcuResource\Pages;
use App\Models\PemeriksaanMcu;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
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
                Section::make('Informasi Pemeriksaan Laboratorium Medis')
                    ->description('Rincian hasil kelayakan dan nomor sertifikat MCU.')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('pasien_id')
                                ->label('Nama Pasien')
                                ->relationship('pasien', 'nama')
                                ->searchable(['nama', 'no_ktp', 'no_paspor'])
                                ->preload()
                                ->required()
                                ->native(false),

                            TextInput::make('nomor_mcu')
                                ->label('Nomor Sertifikat MCU')
                                ->required()
                                ->maxLength(100)
                                ->unique(ignoreRecord: true)
                                ->default(fn () => sprintf('%04d/MCU-KIS/%s', PemeriksaanMcu::whereYear('created_at', date('Y'))->count() + 1, date('Y'))),
                        ]),

                        Grid::make(2)->schema([
                            DatePicker::make('tanggal_pemeriksaan')
                                ->label('Tanggal MCU')
                                ->required()
                                ->default(now()->toDateString())
                                ->native(false),

                            DatePicker::make('tanggal_kedaluwarsa')
                                ->label('Berlaku Sampai')
                                ->required()
                                ->default(now()->addYears(2)->toDateString())
                                ->native(false),
                        ]),

                        TextInput::make('nama_perusahaan')
                            ->label('Perusahaan / Instansi Pemohon')
                            ->maxLength(255),

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

                TextColumn::make('tanggal_pemeriksaan')
                    ->label('Tanggal MCU')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('tanggal_kedaluwarsa')
                    ->label('Berlaku Hingga')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('lampiran_count')
                    ->counts('lampiran')
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
