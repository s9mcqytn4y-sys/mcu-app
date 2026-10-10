<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\LogAuditResource\Pages;
use App\Models\LogAudit;
use App\Models\User;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class LogAuditResource extends Resource
{
    protected static ?string $model = LogAudit::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Log Audit Sistem';
    protected static ?string $modelLabel = 'Log Audit';
    protected static ?string $pluralModelLabel = 'Daftar Log Audit';
    protected static ?string $navigationGroup = 'Keamanan & Tata Kelola';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();
        return $user !== null && $user->apakahAdministrator();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Rincian Log Audit Keamanan')
                    ->description('Catatan audit immutable aktivitas pengguna.')
                    ->schema([
                        TextInput::make('pengguna.name')
                            ->label('Pengguna (User)')
                            ->placeholder('Sistem / Tamu'),

                        TextInput::make('tindakan')
                            ->label('Tindakan Operasi'),

                        TextInput::make('tipe_model')
                            ->label('Tipe Model / Entitas'),

                        TextInput::make('id_model')
                            ->label('ID Model Terkait'),

                        TextInput::make('alamat_ip')
                            ->label('Alamat IP'),

                        TextInput::make('agen_pengguna')
                            ->label('Peramban (User Agent)')
                            ->columnSpanFull(),

                        KeyValue::make('rincian')
                            ->label('Metadata Rincian')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu Kejadian')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                TextColumn::make('pengguna.name')
                    ->label('Pelaku')
                    ->searchable()
                    ->placeholder('Sistem / Publik'),

                TextColumn::make('tindakan')
                    ->label('Tindakan')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pemeriksaan_mcu_dibuat' => 'success',
                        'pemeriksaan_mcu_diperbarui' => 'warning',
                        'AKSES_VERIFIKASI_MCU', 'portal_mcu_diverifikasi' => 'info',
                        'lampiran_mcu_diunduh' => 'primary',
                        default => 'gray',
                    }),

                TextColumn::make('alamat_ip')
                    ->label('Alamat IP')
                    ->searchable()
                    ->fontFamily('mono'),

                TextColumn::make('tipe_model')
                    ->label('Model Terkait')
                    ->formatStateUsing(fn (?string $state): string => class_basename((string) $state)),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLogAudits::route('/'),
            'view' => Pages\ViewLogAudit::route('/{record}'),
        ];
    }
}
