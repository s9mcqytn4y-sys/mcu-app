<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PasienResource\Pages;
use App\Models\Pasien;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PasienResource extends Resource
{
    protected static ?string $model = Pasien::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Data Pasien';
    protected static ?string $modelLabel = 'Pasien';
    protected static ?string $pluralModelLabel = 'Daftar Pasien';
    protected static ?string $navigationGroup = 'Klinik & Operasional';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Demografi Pasien')
                    ->description('Lengkapi data identitas pasien untuk arsip rekam medis.')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        Grid::make(2)->schema([
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
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('no_ktp')
                                ->label('Nomor KTP / NIK')
                                ->maxLength(30)
                                ->unique(ignoreRecord: true),

                            TextInput::make('no_paspor')
                                ->label('Nomor Paspor')
                                ->maxLength(30)
                                ->unique(ignoreRecord: true),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('jenis_kelamin')
                    ->label('Gender')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Laki-laki' => 'info',
                        'Perempuan' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('tanggal_lahir')
                    ->label('Tanggal Lahir')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('no_ktp')
                    ->label('KTP / NIK')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('no_paspor')
                    ->label('Paspor')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('pemeriksaan_mcu_count')
                    ->counts('pemeriksaanMcu')
                    ->label('Riwayat MCU')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options([
                        'Laki-laki' => 'Laki-laki',
                        'Perempuan' => 'Perempuan',
                    ]),
            ])
            ->actions([
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
            'index' => Pages\ListPasiens::route('/'),
            'create' => Pages\CreatePasien::route('/create'),
            'edit' => Pages\EditPasien::route('/{record}/edit'),
        ];
    }
}
