<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\LogAuditResource\Pages;
use App\Models\LogAudit;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LogAuditResource extends Resource
{
    protected static ?string $model = LogAudit::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Log Audit Sistem';
    protected static ?string $modelLabel = 'Log Audit';
    protected static ?string $pluralModelLabel = 'Daftar Log Audit';
    protected static ?string $navigationGroup = 'Keamanan & Tata Kelola';
    protected static ?int $navigationSort = 3;

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
                        TextInput::make('user.name')
                            ->label('Pengguna (User)'),

                        TextInput::make('aksi')
                            ->label('Aksi Operasi'),

                        TextInput::make('tipe_entitas')
                            ->label('Tipe Entitas'),

                        TextInput::make('alamat_ip')
                            ->label('Alamat IP'),

                        TextInput::make('user_agent')
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

                TextColumn::make('user.name')
                    ->label('Pelaku')
                    ->searchable()
                    ->placeholder('Sistem / Tamu'),

                TextColumn::make('aksi')
                    ->label('Aksi')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pemeriksaan_mcu_dibuat' => 'success',
                        'pemeriksaan_mcu_diperbarui' => 'warning',
                        'portal_mcu_diverifikasi' => 'info',
                        'lampiran_mcu_diunduh' => 'primary',
                        default => 'gray',
                    }),

                TextColumn::make('alamat_ip')
                    ->label('Alamat IP')
                    ->searchable()
                    ->fontFamily('mono'),

                TextColumn::make('tipe_entitas')
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
